<?php

namespace App\Models\Microsoft;

use App\Models\SyncMapping;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


/**
 * App\Models\Microsoft\AzureGroup
 *
 * @property string $id
 * @property string $name
 * @property Carbon $synced_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|AzureMembership[] $memberships
 * @property-read int|null $memberships_count
 * @property-read SyncMapping|null $syncMapping
 * @property-read Collection|AzureUser[] $users
 * @property-read int|null $users_count
 * @method static Builder|AzureGroup newModelQuery()
 * @method static Builder|AzureGroup newQuery()
 * @method static Builder|AzureGroup query()
 * @method static Builder|AzureGroup whereCreatedAt($value)
 * @method static Builder|AzureGroup whereId($value)
 * @method static Builder|AzureGroup whereName($value)
 * @method static Builder|AzureGroup whereSyncedAt($value)
 * @method static Builder|AzureGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AzureGroup extends Model
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
        return $this->hasMany(AzureMembership::class);
    }

    public function syncMapping()
    {
        return $this->hasOne(SyncMapping::class);
    }

    public function users()
    {
        return $this->belongsToMany(AzureUser::class)
            ->withPivot('is_owner', 'synced_at');
    }
}
