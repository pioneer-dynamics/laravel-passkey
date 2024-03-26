<?php

namespace PioneerDynamics\LaravelPasskey\Providers;

use App\Tools\Passkey\SvgtasRegistrar;
use Illuminate\Support\ServiceProvider;
use App\Tools\Passkey\SvgtasAuthenticator;
use PioneerDynamics\LaravelPasskey\Console\Commands\PasskeyInstall;
use PioneerDynamics\LaravelPasskey\Contracts\Passkey as ContractsPasskey;
use PioneerDynamics\LaravelPasskey\Passkey\Passkey;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyRegistrar;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator;

class PasskeyServiceProvider extends ServiceProvider
{
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
