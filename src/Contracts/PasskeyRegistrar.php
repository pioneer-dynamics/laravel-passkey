<?php
namespace PioneerDynamics\LaravelPasskey\Contracts;

interface PasskeyRegistrar
{
    /**
     * Generate registration options
     * 
     * @param null|\PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser $passkeyUser
     * @return mixed
     */
    public function generateOptions(?PasskeyUser $passkeyUser = null);

    /**
     * The user related to the passkey
     *
     * @param null|\PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser $passkeyUser
     * @return \PioneerDynamics\LaravelPasskey\Contracts\PasskeyRegistrar
     */
    public function setUser(?PasskeyUser $passkeyUser = null);

    /**
     * Verify the registration challenge response
     * 
     * @param array $data
     * @param array $challenge = null
     * @return \Webauthn\PublicKeyCredentialSource
     * @throws \Exception
     */

    public function validate(array $data, array $challenge = null);

    /**
     * Check if challenge response is valid
     * 
     * @param array $data
     * @param array $challenge = null
     * @return bool|\Webauthn\PublicKeyCredentialSource
     */
    public function validateSilent(array $data, array $challenge = null);
}