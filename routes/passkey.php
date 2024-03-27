<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use PioneerDynamics\LaravelPasskey\Http\Controllers\PasskeyController;

Route::prefix(Config::get('passkey.routes.prefix', 'passkeys'))->group(function () {
    Route::post('/authentication-options', [PasskeyController::class, 'getAuthenticationOptions'])->name('passkeys.authentication-options');
    Route::post('/login', [PasskeyController::class, 'login'])->name('passkeys.login');
});

Route::middleware([
    'web',
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::prefix(Config::get('passkey.routes.prefix', 'passkeys'))->group(function () {
        Route::middleware('password.confirm:,'.Config::get('passkey.password_confirmation_ttl'))->post('/registration-options', [PasskeyController::class, 'getRegistrationOptions'])->name('passkeys.registration-options');
        Route::post('/verify', [PasskeyController::class, 'verify'])->name('passkeys.verify');
        Route::middleware('password.confirm:,'.Config::get('passkey.password_confirmation_ttl'))->delete('/{passkey}', [PasskeyController::class, 'destroy'])->name('passkeys.destroy');
        Route::post('/', [PasskeyController::class, 'store'])->name('passkeys.store');
    });

});