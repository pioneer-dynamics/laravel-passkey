<?php
namespace PioneerDynamics\LaravelPasskey\Traits;

use Illuminate\Support\Facades\Config;

trait HasPasskeys
{
    public function passkeys()
    {
        return $this->morphMany(Config::get('passkey.models.passkey'), "passkeyable");
    }

    public static function bootHasPassKeys()
    {
        static::deleting(function ($user) {
            $user->passkeys()->delete();
        });
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getUserId()
    {
        return $this->id;
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    public function getUserIcon()
    {
        return null;
    }
}