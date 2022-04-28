<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OAuth extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'id',
        'provider',
        'email',
        'access_token',
        'expires_at',
        'refresh_token',
        'user_id'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected function encryptToken($value)
    {
        try {
            decrypt($value);
        } catch (DecryptException $e) {
            $value = encrypt($value);
        }

        return $value;
    }

    public function getAccessTokenAttribute($value)
    {
        return decrypt($value);
    }

    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = $this->encryptToken($value);
    }

    public function getRefreshTokenAttribute($value)
    {
        return decrypt($value);
    }

    public function setRefreshTokenAttribute($value)
    {
        $this->attributes['refresh_token'] = $this->encryptToken($value);
    }

    /**
     * Get the parent identity model (AzureUser or WebexUser).
     */
    public function provider()
    {
        return $this->morphTo(__FUNCTION__, 'provider', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
