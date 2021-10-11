<?php

namespace App\Models;

use Eloquent;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * App\Models\OAuth
 *
 * @property string $id
 * @property Model|Eloquent $provider
 * @property string $email
 * @property string $access_token
 * @property Carbon $expires_at
 * @property string $refresh_token
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|OAuth newModelQuery()
 * @method static Builder|OAuth newQuery()
 * @method static Builder|OAuth query()
 * @method static Builder|OAuth whereAccessToken($value)
 * @method static Builder|OAuth whereCreatedAt($value)
 * @method static Builder|OAuth whereEmail($value)
 * @method static Builder|OAuth whereExpiresAt($value)
 * @method static Builder|OAuth whereId($value)
 * @method static Builder|OAuth whereProvider($value)
 * @method static Builder|OAuth whereRefreshToken($value)
 * @method static Builder|OAuth whereUpdatedAt($value)
 * @method static Builder|OAuth whereUserId($value)
 * @mixin Eloquent
 */
class OAuth extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
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
