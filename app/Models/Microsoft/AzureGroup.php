<?php

namespace App\Models\Microsoft;

use App\Models\SyncMapping;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AzureGroup extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $keyType = 'string';

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
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
            ->using(AzureMembership::class);
    }
}
