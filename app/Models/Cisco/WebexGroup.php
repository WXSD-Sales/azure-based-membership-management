<?php

namespace App\Models\Cisco;

use App\Models\SyncMapping;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WebexGroup extends Model
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
        return $this->hasMany(WebexMembership::class);
    }

    public function syncMapping()
    {
        return $this->hasOne(SyncMapping::class);
    }

    public function users()
    {
        return $this->belongsToMany(WebexUser::class)
            ->using(WebexMembership::class);
    }
}
