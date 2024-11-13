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

        static::retrieved(function ($user) {
            $user->load('passkeys');
        });
    }

    public function getUsername()
    {
        $username_field = config('passkey.database.username');

        return $this->$username_field;
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
        return blank($this->profile_photo_url)
            ? null
            : $this->generateUserImage();
    }

    private function generateUserImage()
    {
        $image = file_get_contents($this->profile_photo_url);

        $file_info = finfo_open(FILEINFO_MIME_TYPE);

        $data = base64_encode($image);

        return 'data: '.finfo_buffer($file_info, $image). ';base64,'.$data;
    }
}