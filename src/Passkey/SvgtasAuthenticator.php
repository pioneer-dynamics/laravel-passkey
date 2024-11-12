<?php
namespace PioneerDynamics\LaravelPasskey\Passkey;

use Illuminate\Support\Facades\Config;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser;
use PioneerDynamics\LaravelPasskey\Passkey\SvgtasPasskey;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator;

class SvgtasAuthenticator extends SvgtasPasskey implements PasskeyAuthenticator
{
    private $passkeyUser;

    /**
     * Generate the authentication options
     * 
     * @param \PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser|null $passkeyUser
     * @return array
     */
    public function generateOptions(?PasskeyUser $passkeyUser = null)
    {
        $this->setUser($passkeyUser);

        return json_decode($this->webauthn->authenticate()->toJson(), true );
    }

    /**
     * Set the user
     * 
     * @param \PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser|null $passkeyUser
     * @return \PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator
     */
    public function setUser(?PasskeyUser $passkeyUser = null)
    {
        if($passkeyUser)
            $passkeyUser->passkeys->each(fn($passkey) => $this->webauthn->allowCredentials->add($passkey->credential_id));
        else
            $this->webauthn->userVerification->preferred();

        $this->passkeyUser = $passkeyUser;

        return $this;
    }

    public function validate(array $data, ?array $challenge = null)
    {
        $response = $this->webauthn->authenticate()->response(json_encode($data));
        
        if($this->passkeyUser)
            $passkey = $this->passkeyUser->passkeys()->credential($response['credentialId'])->firstOrFail();
        else
            $passkey = Config::get('passkey.models.passkey')::credential($response['credentialId'])->firstOrFail();

        return $this->webauthn->authenticate()->validate($passkey->public_key);
    }
}