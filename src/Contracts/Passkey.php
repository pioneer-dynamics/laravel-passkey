<?php
namespace PioneerDynamics\LaravelPasskey\Contracts;

interface Passkey
{

    /**
     * Get the registrar instance
     * 
     * @return \PioneerDynamics\LaravelPasskey\Contracts\Registrar
     */
    public function registrar();

    /**
     * Get the authenticator instance
     * 
     * @return \PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator
     */
    public function authenticator();

    /**
     * Configure the application identity for passkey to use
     * 
     * @param callable $closure
     */
    public static function configureApplicationIdentityUsing(callable $closure);

    /**
     * Get the application's identity
     * 
     * @return array
     */
    public static function getApplicationIdentity();

    /**
     * Create the passkey model
     * 
     * @param callable $closure
     * @return void
     */
    public static function createModelUsing(callable $closure);
    
    /**
     * Update the passkey model
     * 
     * @param callable $closure
     * @return void
     */
    public static function updateModelUsing(callable $closure);
}