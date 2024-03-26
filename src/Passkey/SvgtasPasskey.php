<?php
namespace PioneerDynamics\LaravelPasskey\Passkey;

use Svgta\WebAuthn\client;
use Illuminate\Support\Facades\Config;

/**
 * @method \Webauthn\PublicKeyCredentialSource validate(array $data, array $challenge = null);
 */
class SvgtasPasskey
{
    public function __construct(protected client $webauthn) 
    {
        if(Config::get('passkey.session', null))
            $this->webauthn->setSessionKey(Config::get('passkey.session'));
        
        $this->createApplicationEntity();

        $this->webauthn->timeout(600000);
    }
    
    /**
     * Creates the application entity
     * 
     * @return PublicKeyCredentialRpEntity
     */
    protected function createApplicationEntity()
    {
        $application = Passkey::getApplicationIdentity();

        $this->webauthn->rp->set(
            $application['name'],
            $application['domain'],
            $application['logo']
        );
    }

    public function validateSilent(array $data, ?array $challenge = null)
    {
        return rescue(fn() => $this->validate($data, $challenge), false);
    }
}