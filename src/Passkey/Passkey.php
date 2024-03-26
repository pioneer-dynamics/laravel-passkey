<?php
namespace PioneerDynamics\LaravelPasskey\Passkey;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyRegistrar;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator;
use PioneerDynamics\LaravelPasskey\Contracts\Passkey as ContractsPasskey;

class Passkey implements ContractsPasskey
{
    /**
     * @var callable|null
     */
    private static $applicationConfigurationClosure;

    /**
     * @var callable|null
     */
    public static $createModelCallback;
    
    /**
     * @var callable|null
     */
    public static $updateModelCallback;

    /**
     * -----------------------------------------------------------
     * Other variables
     * -----------------------------------------------------------
     * 
     * @var \PioneerDynamics\LaravelPasskey\Contracts\PasskeyRegistrar $registrar
     */
    public function __construct(private PasskeyRegistrar $registrar, private PasskeyAuthenticator $authenticator) { }

    /**
     * Get the registrar instance
     * 
     * @return \PioneerDynamics\LaravelPasskey\Contracts\PasskeyRegistrar
     */
    public function registrar()
    {
        return $this->registrar;
    }

    /**
     * Get the authenticator instance
     * 
     * @return \PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator
     */
    public function authenticator()
    {
        return $this->authenticator;
    }

    /**
     * Configure the application identity for passkey to use
     * 
     * @param callable $closure
     * 
     * Example:
     */
    public static function configureApplicationIdentityUsing(callable $closure)
    {
        static::$applicationConfigurationClosure = $closure;
    }

    /**
     * Get the application's identity
     * 
     * @return array
     */
    public static function getApplicationIdentity()
    {
        return isset(static::$applicationConfigurationClosure) && is_callable(static::$applicationConfigurationClosure) 
            ? call_user_func(static::$applicationConfigurationClosure)
            : [
                'name' => Config::get('app.name'),
                'domain' => parse_url(Config::get('app.url'), PHP_URL_HOST),
                'logo' => Config::get('passkey.logo', null),
            ];
    }

    /**
     * Create the passkey model
     * 
     * @param callable $closure
     * @return void
     */
    public static function createModelUsing(callable $closure)
    {
        static::$createModelCallback = $closure;
    }
    
    /**
     * Update the passkey model
     * 
     * @param callable $closure
     * @return void
     */
    public static function updateModelUsing(callable $closure)
    {
        static::$updateModelCallback = $closure;
    }
}