<?php

namespace App\Models;

use App\Models\Cisco\WebexGroup;
use App\Models\Microsoft\AzureGroup;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * App\Models\SyncMapping
 *
 * @property int $id
 * @property string $azure_group_id
 * @property string $webex_group_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AzureGroup $azureGroup
 * @property-read WebexGroup $webexGroup
 * @method static Builder|SyncMapping newModelQuery()
 * @method static Builder|SyncMapping newQuery()
 * @method static Builder|SyncMapping query()
 * @method static Builder|SyncMapping whereAzureGroupId($value)
 * @method static Builder|SyncMapping whereCreatedAt($value)
 * @method static Builder|SyncMapping whereId($value)
 * @method static Builder|SyncMapping whereUpdatedAt($value)
 * @method static Builder|SyncMapping whereUserId($value)
 * @method static Builder|SyncMapping whereWebexGroupId($value)
 * @mixin Eloquent
 */
class SyncMapping extends Model
{
    use HasFactory;

    public function azureGroup()
    {
        return $this->belongsTo(AzureGroup::class);
    }

    public function webexGroup()
    {
        return $this->belongsTo(WebexGroup::class);
    }
}
