<?php
namespace PioneerDynamics\LaravelPasskey\Facades;

use Illuminate\Support\Facades\Facade;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator as ContractsPasskeyAuthenticator;

/**
 * @method static mixed generateOptions(?\PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser $passkeyUser = null)
 * @method static \PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticatory setUser(?\PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser $passkeyUser = null)
 * @method static \PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticatory setChallenge(?array $challenge = null)
 * @method static \Webauthn\PublicKeyCredentialSource validate(string $data, array $challenge = null)
 * @method static bool validateSilent(string $data, array $challenge = null)
 */
class PasskeyAuthenticator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ContractsPasskeyAuthenticator::class;
    }
}