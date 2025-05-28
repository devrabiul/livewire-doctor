<?php

namespace Devrabiul\LivewireDoctor;

use Livewire\Livewire;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Devrabiul\LivewireDoctor\Commands\LivewireDoctorCommand;

class LivewireDoctorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * This method is called after all other services have been registered,
     * allowing you to perform actions like route registration, publishing assets,
     * and configuration adjustments.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->updateProcessingDirectoryConfig();
        $this->handleVersionedPublishing();

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * This method registers:
     * - Configuration file publishing to the application's config directory.
     * - Asset publishing to the public vendor directory, replacing old assets if found.
     *
     * It is typically called when the application is running in console mode
     * to enable artisan vendor:publish commands.
     *
     * @return void
     */
    private function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/config/livewire-doctor.php' => config_path('livewire-doctor.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * This method:
     * - Loads the package config file if not already loaded.
     * - Registers a singleton instance of the LivewireDoctor class in the Laravel service container.
     *
     * This allows other parts of the application to resolve the 'LivewireDoctor' service.
     *
     * @return void
     */
    public function register(): void
    {
        $configPath = config_path('livewire-doctor.php');

        if (!file_exists($configPath)) {
            config(['livewire-doctor' => require __DIR__ . '/config/livewire-doctor.php']);
        }

        $this->app->singleton('LivewireDoctor', function ($app) {
            return new LivewireDoctor($app['session'], $app['config']);
        });

        // ✅ Register command
        if ($this->app->runningInConsole()) {
            $this->commands([
                LivewireDoctorCommand::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * This method is used by Laravel's deferred providers mechanism
     * and lists the services that this provider registers.
     *
     * @return array<string> Array of service container binding keys provided by this provider.
     */
    public function provides(): array
    {
        return ['LivewireDoctor'];
    }

    /**
     * Determine and set the 'system_processing_directory' configuration value.
     *
     * This detects if the current PHP script is being executed from the public directory
     * or the project root directory, or neither, and sets a config value accordingly:
     *
     * - 'public' if script path equals public_path()
     * - 'root' if script path equals base_path()
     * - 'unknown' otherwise
     *
     * This config can be used internally to adapt asset loading or paths.
     *
     * @return void
     */
    private function updateProcessingDirectoryConfig(): void
    {
        $scriptPath = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
        $basePath   = realpath(base_path());
        $publicPath = realpath(public_path());

        if ($scriptPath === $publicPath) {
            $systemProcessingDirectory = 'public';
        } elseif ($scriptPath === $basePath) {
            $systemProcessingDirectory = 'root';
        } else {
            $systemProcessingDirectory = 'unknown';
        }

        config(['livewire-doctor.system_processing_directory' => $systemProcessingDirectory]);
    }

    /**
     * Get the current installed version of the package from composer.lock.
     *
     * Reads and parses the composer.lock file located at the project root,
     * searches for the package 'devrabiul/livewire-doctor',
     * and returns the version string if found.
     *
     * Returns null if:
     * - composer.lock does not exist
     * - package is not found in composer.lock
     *
     * @return string|null Version string of the installed package, e.g. "1.0.1" or null if unavailable.
     */
    private function getCurrentVersion(): ?string
    {
        $lockFile = base_path('composer.lock');
        if (!file_exists($lockFile)) {
            return null;
        }

        $lockData = json_decode(file_get_contents($lockFile), true);
        $packages = $lockData['packages'] ?? [];

        foreach ($packages as $package) {
            if ($package['name'] === 'livewire/livewire') {
                return $package['version'];
            }
        }

        return null;
    }

    /**
     * Get the version recorded in the published version.php file.
     *
     * This file is expected to be located at:
     * `public/vendor/devrabiul/livewire-doctor/version.php`
     *
     * If the file exists and returns an array with a 'version' key,
     * that version string is returned.
     *
     * Returns null if the file does not exist or does not contain a version.
     *
     * @return string|null Previously published version string or null if none found.
     */
    private function getPublishedVersion(): ?string
    {
        $versionFile = public_path('vendor/devrabiul/livewire-doctor/version.php');

        if (!File::exists($versionFile)) {
            return null;
        }

        $versionData = include $versionFile;

        return $versionData['version'] ?? null;
    }

    /**
     * Publish the assets if the current package version differs from the published version.
     *
     * This method performs the following steps:
     * - Retrieves the current installed package version.
     * - Retrieves the previously published version from the public directory.
     * - If versions differ (or no published version exists), deletes the existing assets folder.
     * - Copies the new assets from the package's `assets` directory to the public vendor folder.
     * - Writes/updates the version.php file in the public folder with the current version.
     *
     * This ensures the public assets are always in sync with the installed package version.
     *
     * @return void
     */
    private function handleVersionedPublishing(): void
    {
        $currentVersion = $this->getCurrentVersion();
        $publishedVersion = $this->getPublishedVersion();

        if ($currentVersion && $currentVersion !== $publishedVersion) {
            $assetsPath = public_path('vendor/devrabiul/livewire-doctor/dist');
            $sourceAssets = base_path('vendor/livewire/livewire/dist');

            // Ensure source assets exist before proceeding
            if (!File::exists($sourceAssets)) {
                logger()->warning("[LivewireDoctor] Source assets directory not found at: $sourceAssets");
                return;
            }

            // Delete and re-create the target directory
            if (File::exists($assetsPath)) {
                File::deleteDirectory($assetsPath);
            }

            File::ensureDirectoryExists($assetsPath);

            // Copy the new assets
            File::copyDirectory($sourceAssets, $assetsPath);

            // Create version.php file with current version
            $versionPhpContent = "<?php\n\nreturn [\n    'version' => '{$currentVersion}',\n];\n";
            File::put(public_path('vendor/devrabiul/livewire-doctor/version.php'), $versionPhpContent);

            logger()->info("[LivewireDoctor] Assets published for version: $currentVersion");
        }
    }
}

