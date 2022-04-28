<?php

namespace App\Models;

use App\Models\Cisco\WebexGroup;
use App\Models\Microsoft\AzureGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SyncMapping extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'azure_group_id',
        'webex_group_id',
        'user_id'
    ];

    public function azureGroup()
    {
        return $this->belongsTo(AzureGroup::class);
    }

    public function webexGroup()
    {
        return $this->belongsTo(WebexGroup::class);
    }
}
