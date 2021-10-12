<?php

namespace App\Models\Cisco;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;


/**
 * App\Models\Cisco\WebexMembership
 *
 * @property string $id
 * @property string $webex_group_id
 * @property string $webex_user_id
 * @property bool|null $is_moderator
 * @property Carbon $synced_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WebexGroup $webexGroup
 * @property-read WebexUser $webexUser
 * @method static Builder|WebexMembership newModelQuery()
 * @method static Builder|WebexMembership newQuery()
 * @method static Builder|WebexMembership query()
 * @method static Builder|WebexMembership whereCreatedAt($value)
 * @method static Builder|WebexMembership whereId($value)
 * @method static Builder|WebexMembership whereIsModerator($value)
 * @method static Builder|WebexMembership whereSyncedAt($value)
 * @method static Builder|WebexMembership whereUpdatedAt($value)
 * @method static Builder|WebexMembership whereWebexGroupId($value)
 * @method static Builder|WebexMembership whereWebexUserId($value)
 * @mixin Eloquent
 */
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
