<?php

namespace App\Models\Microsoft;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

class AzureMembership extends Pivot
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
