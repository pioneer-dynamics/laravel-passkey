<?php
namespace PioneerDynamics\LaravelPasskey\Facades;

use Illuminate\Support\Facades\Facade;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyRegistrar as ContractsPasskeyRegistrar;

/**
 * @method static mixed generateOptions(?\PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser $passkeyUser = null)
 * @method static \PioneerDynamics\LaravelPasskey\Contracts\PasskeyRegistrar setUser(?\PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser $passkeyUser = null)
 * @method static \Webauthn\PublicKeyCredentialSource validate(string $data, array $challenge = null)
 * @method static bool validateSilent(string $data, array $challenge = null)
 */
class PasskeyRegistrar extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ContractsPasskeyRegistrar::class;
    }
}