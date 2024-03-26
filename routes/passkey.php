<?php

use Illuminate\Support\Facades\Route;
use PioneerDynamics\LaravelPasskey\Http\Controllers\PasskeyController;

Route::post('/passkeys/authentication-options', [PasskeyController::class, 'getAuthenticationOptions'])->name('passkeys.authentication-options');
Route::post('/passkeys/login', [PasskeyController::class, 'login'])->name('passkeys.login');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::middleware('password.confirm:,60')->post('/passkeys/registration-options', [PasskeyController::class, 'getRegistrationOptions'])->name('passkeys.registration-options');
    Route::post('/passkeys/verify', [PasskeyController::class, 'verify'])->name('passkeys.verify');
    Route::middleware('password.confirm:,60')->delete('/passkeys/{passkey}', [PasskeyController::class, 'destroy'])->name('passkeys.destroy');
    Route::post('/passkeys', [PasskeyController::class, 'store'])->name('passkeys.store');

});