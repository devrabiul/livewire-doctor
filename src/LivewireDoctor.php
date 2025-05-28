<?php

namespace Devrabiul\LivewireDoctor;

use Livewire\Livewire;
use Illuminate\Config\Repository as Config;
use Illuminate\Session\SessionManager as Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/**
 * Class LivewireDoctor
 *
 * Handles the integration of Livewire assets for customized publishing,
 * specifically for replacing the default Livewire JavaScript with a custom path.
 */
class LivewireDoctor
{
    /**
     * The session manager instance.
     *
     * @var \Illuminate\Session\SessionManager
     */
    protected Session $session;

    /**
     * The configuration repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected Config $config;

    /**
     * LivewireDoctor constructor.
     *
     * @param \Illuminate\Session\SessionManager $session The session manager instance.
     * @param \Illuminate\Contracts\Config\Repository $config The configuration repository instance.
     */
    public function __construct(Session $session, Config $config)
    {
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * Initializes and sets custom Livewire asset and update routes.
     *
     * This method:
     * - Checks if a custom `livewire.js` exists in the public directory and copies it from the vendor directory if missing.
     * - Overrides the default Livewire script route to point to the custom asset.
     * - Overrides the Livewire update route for dynamic Livewire component handling.
     *
     * @return void
     */
    public static function initCustomAsset(): void
    {
        // Define paths
        $customAssetPath = public_path('vendor/devrabiul/livewire-doctor/dist/livewire.js');
        $sourceAssetPath = base_path('vendor/livewire/livewire/dist/livewire.js');

        // Copy asset if not already published
        if (!File::exists($customAssetPath) && File::exists($sourceAssetPath)) {
            File::ensureDirectoryExists(dirname($customAssetPath));
            File::copy($sourceAssetPath, $customAssetPath);
        }

        // Define custom Livewire script route
        Livewire::setScriptRoute(function ($handle) {
            $scriptPath = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
            $basePath   = realpath(base_path());
            $publicPath = realpath(public_path());

            $systemProcessingDirectory = $scriptPath === $publicPath ? 'public'
                : ($scriptPath === $basePath ? 'root' : 'unknown');

            $basePathSegment = trim(request()->getBasePath(), '/');

            $livewireJsPath = match ($systemProcessingDirectory) {
                'public' => $basePathSegment
                    ? "/{$basePathSegment}/vendor/devrabiul/livewire-doctor/dist/livewire.js"
                    : "/vendor/devrabiul/livewire-doctor/dist/livewire.js",
                default => $basePathSegment
                    ? "/{$basePathSegment}/public/vendor/devrabiul/livewire-doctor/dist/livewire.js"
                    : "/public/vendor/devrabiul/livewire-doctor/dist/livewire.js",
            };

            return Route::get($livewireJsPath, $handle);
        });

        // Define custom Livewire update route
        Livewire::setUpdateRoute(function ($handle) {
            $scriptPath = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
            $basePath   = realpath(base_path());
            $publicPath = realpath(public_path());

            $systemProcessingDirectory = $scriptPath === $publicPath ? 'public'
                : ($scriptPath === $basePath ? 'root' : 'unknown');

            $basePathSegment = trim(request()->getBasePath(), '/');

            $livewireUpdatePath = match ($systemProcessingDirectory) {
                'public' => $basePathSegment
                    ? "/{$basePathSegment}/livewire/update"
                    : "/livewire/update",
                default => $basePathSegment
                    ? "/{$basePathSegment}/public/livewire/update"
                    : "/public/livewire/update",
            };

            return Route::post($livewireUpdatePath, $handle);
        });
    }
}