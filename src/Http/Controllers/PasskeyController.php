<?php
namespace PioneerDynamics\LaravelPasskey\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyRegistrar;
use PioneerDynamics\LaravelPasskey\Http\Controllers\Controller;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyAuthenticator;
use PioneerDynamics\LaravelPasskey\Passkey\Passkey as PasskeyTool;
use PioneerDynamics\LaravelPasskey\Http\Requests\StorePasskeyRequest;
use PioneerDynamics\LaravelPasskey\Http\Requests\VerifyPasskeyRequest;

class PasskeyController extends Controller
{
    public function getSessionKey()
    {
        return config('passkey.session');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return app('passkey.views.management');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getRegistrationOptions(PasskeyRegistrar $passkeyRegistrar, Request $request)
    {
        return back()->with('flash', [
            'options' => $passkeyRegistrar->setUser($request->user())->generateOptions()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePasskeyRequest $request)
    {
        if(isset(PasskeyTool::$createModelCallback) && is_callable(PasskeyTool::$createModelCallback)) {
            app()->call(PasskeyTool::$createModelCallback);
        }
        else
        {
            $request->user()->passkeys()->create([
                'name' => $request->name,
                'credential_id' => $request->publicKeyCredentialSource['credentialId'],
                'public_key' => $request->publicKeyCredentialSource['jsonData'],
            ]);
        }
    }

    /**
     * Get the autnentication options
     */
    public function getAuthenticationOptions(PasskeyAuthenticator $passkeyAuthenticator, Request $request)
    {
        $usernameField = Config::get('passkey.database.username');

        $request->validate([
            $usernameField => ['required', __('exists::table,:field', [
                'table' => Config::get('passkey.database.table'),
                'field' => $usernameField,
            ])],
        ]);

        $username = optional($request->user())->$usernameField ?? $request->$usernameField;

        $user = Config::get('passkey.models.user')::where($usernameField, $username)->first();

        return back()->with('flash', [
            'options' => $user->passkeys->count() 
                            ? $passkeyAuthenticator->setUser($user)->generateOptions()
                            : false,
        ]);
    }

    /**
     * Verify user using passkey
     */
    public function verify(VerifyPasskeyRequest $request)
    {
        if(isset(PasskeyTool::$updateModelCallback) && is_callable(PasskeyTool::$updateModelCallback)) {
            app()->call(PasskeyTool::$updateModelCallback);
        }
        else
        {
            $pk = json_decode($request->publicKeyCredentialSource, true);
            Config::get('passkey.models.passkey')::credential($pk['credential']['id'])
                ->user($pk['userHandle'])
                ->update([
                    'public_key' => $request->publicKeyCredentialSource,
                ]);
        }

        $request->session()->passwordConfirmed();

        return back()->with('flash', [
            'verified' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function login(VerifyPasskeyRequest $request)
    {
        if(isset(PasskeyTool::$updateModelCallback) && is_callable(PasskeyTool::$updateModelCallback)) {
            app()->call(PasskeyTool::$updateModelCallback);
        }
        else
        {
            $pk = json_decode($request->publicKeyCredentialSource, true);

            Config::get('passkey.models.passkey')::credential($pk['credential']['id'])
                ->user($pk['userHandle'])
                ->update([
                    'public_key' => $request->publicKeyCredentialSource,
                ]);
        }

        Auth::loginUsingId($pk['userHandle'], $request->remember == 'on');

        return redirect()->intended(Config::get('passkey.home'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($passkey)
    {
        $passkey = Config::get('passkey.models.passkey')::where('id', $passkey)->firstOrFail();

        $passkey->delete();
    }
}
