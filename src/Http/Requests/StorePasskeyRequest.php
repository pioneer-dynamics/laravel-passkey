<?php

namespace PioneerDynamics\LaravelPasskey\Http\Requests;

use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use PioneerDynamics\LaravelPasskey\Facades\PasskeyRegistrar;

class StorePasskeyRequest extends FormRequest
{
    public $publicKeyCredentialSource;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

    public function after(): array
    {
        return [
            function(Validator $validator)
            {
                $this->publicKeyCredentialSource = PasskeyRegistrar::validate($this->passkey);

                if(!$this->publicKeyCredentialSource)
                    $validator->errors()->add('passkey', __('Invalid passkey.'));
            }
        ];
    }
}
