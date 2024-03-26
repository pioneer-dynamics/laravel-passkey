<?php
namespace PioneerDynamics\LaravelPasskey\Contracts;

interface PasskeyAuthenticator
{
    /**
     * Generate the authentication options
     * 
     * @param \PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser|null $passkeyUser
     * @return array
     */
    public function generateOptions(?PasskeyUser $passkeyUser = null);

    /**
     * Set the user
     * 
     * @param \PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser|null $passkeyUser
     * @return \PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator
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