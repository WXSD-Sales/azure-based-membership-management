<?php

namespace App\Models\Microsoft;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * App\Models\Microsoft\AzureMembership
 *
 * @property int $id
 * @property string $azure_group_id
 * @property string $azure_user_id
 * @property bool|null $is_owner
 * @property Carbon $synced_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AzureGroup $azureGroup
 * @property-read AzureUser $azureUser
 * @method static Builder|AzureMembership newModelQuery()
 * @method static Builder|AzureMembership newQuery()
 * @method static Builder|AzureMembership query()
 * @method static Builder|AzureMembership whereAzureGroupId($value)
 * @method static Builder|AzureMembership whereAzureUserId($value)
 * @method static Builder|AzureMembership whereCreatedAt($value)
 * @method static Builder|AzureMembership whereId($value)
 * @method static Builder|AzureMembership whereIsOwner($value)
 * @method static Builder|AzureMembership whereSyncedAt($value)
 * @method static Builder|AzureMembership whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AzureMembership extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $table = 'azure_group_azure_user';

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'id',
        'azure_group_id',
        'azure_user_id',
        'is_owner',
        'synced_at'
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'synced_at' => 'datetime',
        'is_owner' => 'boolean'
    ];

    public function azureGroup()
    {
        return $this->belongsTo(AzureGroup::class);
    }

    public function azureUser()
    {
        return $this->belongsTo(AzureUser::class);
    }
}
