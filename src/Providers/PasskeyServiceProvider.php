<?php

namespace PioneerDynamics\LaravelPasskey\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\AboutCommand;
use PioneerDynamics\LaravelPasskey\Passkey\Passkey;
use PioneerDynamics\LaravelPasskey\Passkey\SvgtasRegistrar;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyRegistrar;
use PioneerDynamics\LaravelPasskey\Passkey\SvgtasAuthenticator;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator;
use PioneerDynamics\LaravelPasskey\Console\Commands\PasskeyInstall;
use PioneerDynamics\LaravelPasskey\Contracts\Passkey as ContractsPasskey;

class PasskeyServiceProvider extends ServiceProvider
{
    const VERSION = '1.0.0';

    const CONFIG_FILE = __DIR__ . '/../../config/passkey.php';

    const MIGRATIONS_PATH = __DIR__ . '/../../database/migrations/';
    
    const VUE_COMPONENTS_PATH = __DIR__ . '/../../resources/js/Components/';

    CONST VUE_PROFILES_PARTIALS_PATH = __DIR__ . '/../../resources/js/Pages/Profile/Partials';

    CONST VUE_LOGIN_PATH = __DIR__ . '/../../resources/js/Pages/Auth';

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->bindClasses();

        $this->mergeConfigFrom(self::CONFIG_FILE, 'passkey');

        $this->defineCommands();
    }

    private function bindClasses()
    {
        $this->app->bind(PasskeyRegistrar::class, SvgtasRegistrar::class);
        $this->app->bind(PasskeyAuthenticator::class, SvgtasAuthenticator::class);
        $this->app->singleton(ContractsPasskey::class, Passkey::class);
    }

    private function defineCommands()
    {
        $this->app->bind('command.passkey:install', PasskeyInstall::class);

        $this->commands([
            'command.passkey:install'
        ]);
    }

    public function boot(): void
    {
        $this->definePublishableAssets();

        $this->defineRoutes();

        $this->defineAbout();
    }

    public function defineAbout()
    {
        AboutCommand::add('PioneerDynamics/LaravelPasskey', fn() => [
            'Version' => self::VERSION,
        ]);
    }

    private function defineRoutes()
    {
        if(Config::get('passkey.routes.enabled', true))
            $this->loadRoutesFrom(__DIR__ . '/../../routes/passkey.php');
    }

    private function definePublishableAssets()
    {
        $this->defineMigrations();

        $this->defineConfigFile();

        $this->defineVueCompoenents();
    }

    private function defineVueCompoenents()
    {
        $this->publishes([
            self::VUE_COMPONENTS_PATH => resource_path('js/Components'),
        ], 'vue-components');

        $this->publishes([
            self::VUE_PROFILES_PARTIALS_PATH => resource_path('js/Pages/Profile/Partials'),
        ], 'vue-components');
        
        $this->publishes([
            self::VUE_LOGIN_PATH => resource_path('js/Pages/Auth'),
        ], 'vue-components');
    }

    private function defineMigrations()
    {
        $this->publishes([
           self::MIGRATIONS_PATH => database_path('migrations'),
        ], 'migrations');
    }
    
    private function defineConfigFile()
    {
        $this->publishes([
            self::CONFIG_FILE => config_path('passkey.php'),
        ], 'config');
    }
}
