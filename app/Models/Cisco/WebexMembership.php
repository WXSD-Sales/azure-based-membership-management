<?php

namespace App\Models\Cisco;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

class WebexMembership extends Pivot
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $table = 'webex_group_webex_user';

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
        'webex_group_id',
        'webex_user_id',
        'is_moderator',
        'synced_at'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'synced_at' => 'datetime',
        'is_moderator' => 'boolean'
    ];

    public function webexGroup()
    {
        return $this->belongsTo(WebexGroup::class);
    }

    public function webexUser()
    {
        return $this->belongsTo(WebexUser::class);
    }
}
