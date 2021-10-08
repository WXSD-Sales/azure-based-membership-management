<?php

namespace App\Models\Cisco;

use App\Models\SyncMapping;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


/**
 * App\Models\Cisco\WebexGroup
 *
 * @property string $id
 * @property string|null $name
 * @property Carbon $synced_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|WebexMembership[] $memberships
 * @property-read int|null $memberships_count
 * @property-read SyncMapping|null $syncMapping
 * @property-read Collection|WebexUser[] $users
 * @property-read int|null $users_count
 * @method static Builder|WebexGroup newModelQuery()
 * @method static Builder|WebexGroup newQuery()
 * @method static Builder|WebexGroup query()
 * @method static Builder|WebexGroup whereCreatedAt($value)
 * @method static Builder|WebexGroup whereId($value)
 * @method static Builder|WebexGroup whereName($value)
 * @method static Builder|WebexGroup whereSyncedAt($value)
 * @method static Builder|WebexGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class WebexGroup extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
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

    public function memberships()
    {
        return $this->hasMany(WebexMembership::class);
    }

    public function syncMapping()
    {
        return $this->hasOne(SyncMapping::class);
    }

    public function users()
    {
        return $this->belongsToMany(WebexUser::class)
            ->withPivot('is_moderator', 'synced_at');
    }
}
