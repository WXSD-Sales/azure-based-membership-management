<?php

namespace App\Models\Microsoft;

use App\Models\OAuth;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\Microsoft\AzureUser
 *
 * @property string $id
 * @property string|null $name
 * @property string $email
 * @property Carbon $synced_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|AzureGroup[] $groups
 * @property-read int|null $groups_count
 * @property-read Collection|AzureMembership[] $memberships
 * @property-read int|null $memberships_count
 * @property-read OAuth|null $oauth
 * @property-read User|null $user
 * @method static Builder|AzureUser newModelQuery()
 * @method static Builder|AzureUser newQuery()
 * @method static Builder|AzureUser query()
 * @method static Builder|AzureUser whereCreatedAt($value)
 * @method static Builder|AzureUser whereEmail($value)
 * @method static Builder|AzureUser whereId($value)
 * @method static Builder|AzureUser whereName($value)
 * @method static Builder|AzureUser whereSyncedAt($value)
 * @method static Builder|AzureUser whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 */
class AzureUser extends Model
{
    use HasFactory, Notifiable;

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
        return $this->belongsToMany(AzureGroup::class)
            ->using(AzureMembership::class);
    }

    public function oauth()
    {
        return $this->morphOne(OAuth::class, 'provider');
    }

    public function memberships()
    {
        return $this->hasMany(AzureMembership::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
