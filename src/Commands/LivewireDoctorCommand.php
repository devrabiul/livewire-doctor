<?php

namespace Devrabiul\LivewireDoctor\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;

class LivewireDoctorCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'livewire:doctor';

    /**
     * The console command description.
     */
    protected $description = 'Run a health check for your Livewire installation.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('🩺 Running Livewire health check...');

        $this->checkLivewireInstalled();
        $this->checkAssetsPublished();
        $this->checkBladeDirectives();
        $this->checkComponentStructure();

        $this->info('✅ Health check complete!');
    }

    protected function checkLivewireInstalled(): void
    {
        if (!class_exists(\Livewire\Livewire::class)) {
            $this->error('❌ Livewire is not installed. Please run: composer require livewire/livewire');
        } else {
            $version = \Composer\InstalledVersions::getVersion('livewire/livewire') ?? 'unknown';
            $this->info("✅ Livewire is installed. Version: $version");
        }
    }

    protected function checkAssetsPublished(): void
    {
        $publicPath = public_path('vendor/livewire/livewire.js');

        if (!File::exists($publicPath)) {
            $this->warn('⚠️ Livewire assets not found. Run: php artisan livewire:publish --assets');
        } else {
            $this->info('✅ Livewire assets are published.');
        }
    }

    protected function checkBladeDirectives(): void
    {
        // Suggest checking master layout manually
        $this->info("🧠 Please ensure you have @livewireStyles in <head> and @livewireScripts before </body>.");
    }

    protected function checkComponentStructure(): void
    {
        $componentPath = app_path('Livewire');

        if (!File::isDirectory($componentPath)) {
            $this->warn('⚠️ No Livewire components found in: ' . $componentPath);
        } else {
            $components = collect(File::allFiles($componentPath))
                ->filter(fn($file) => $file->getExtension() === 'php')
                ->map(fn($file) => $file->getFilename());

            if ($components->isEmpty()) {
                $this->warn('⚠️ No Livewire component classes detected.');
            } else {
                $this->info("✅ Found " . $components->count() . " Livewire components.");
            }
        }
    }
}
