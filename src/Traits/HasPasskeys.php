<?php
namespace PioneerDynamics\LaravelPasskey\Traits;

use PioneerDynamics\LaravelPasskey\Models\Passkey;

trait HasPasskeys
{
    public function passkeys()
    {
        return $this->morphMany(Passkey::class, "passkeyable");
    }

    public function bootHasPassKeys()
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