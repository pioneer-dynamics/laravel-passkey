<?php

namespace PioneerDynamics\LaravelPasskey\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Config;

class Passkey extends Model
{
    protected $fillable = [
        'public_key',
        'credential_id',
        'name',
    ];

    protected function passkeyable()
    {
        return $this->morphTo();
    }

    public function credentialId(): Attribute {
        return new Attribute(
            get: fn ($value) => base64_decode($value),
            set: fn ($value) => base64_encode($value),
        );
    }

    public function scopeCredential($query, $value)
    {
        return $query->where('credential_id', base64_encode($value));
    }

    public function scopeUser($query, $value)
    {
        return $query->where('passkeyable_id', $value)->where('passkeyable_type', Config::get('passkey.user_model'));
    }
}
