<?php

namespace App\Models\Cisco;

use App\Models\Microsoft\AzureMembership;
use App\Models\OAuth;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


/**
 * App\Models\Cisco\WebexUser
 *
 * @property string $id
 * @property string|null $name
 * @property string $email
 * @property Carbon $synced_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|WebexGroup[] $groups
 * @property-read int|null $groups_count
 * @property-read Collection|WebexMembership[] $memberships
 * @property-read int|null $memberships_count
 * @property-read OAuth|null $oauth
 * @property-read User|null $user
 * @method static Builder|WebexUser newModelQuery()
 * @method static Builder|WebexUser newQuery()
 * @method static Builder|WebexUser query()
 * @method static Builder|WebexUser whereCreatedAt($value)
 * @method static Builder|WebexUser whereEmail($value)
 * @method static Builder|WebexUser whereId($value)
 * @method static Builder|WebexUser whereName($value)
 * @method static Builder|WebexUser whereSyncedAt($value)
 * @method static Builder|WebexUser whereUpdatedAt($value)
 * @mixin Eloquent
 */
class WebexUser extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email',
        'synced_at'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'synced_at' => 'datetime',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::limit($value);
    }

    public function groups()
    {
        return $this->belongsToMany(WebexGroup::class)
            ->withPivot('is_moderator', 'synced_at');
    }

    public function oauth()
    {
        return $this->morphOne(OAuth::class, 'provider');
    }

    public function memberships()
    {
        return $this->hasMany(WebexMembership::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
