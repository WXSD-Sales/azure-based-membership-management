<?php

namespace App\Jobs;

use App\Models\Microsoft\AzureGroup;
use App\Models\Microsoft\AzureMembership;
use App\Models\Microsoft\AzureUser;
use App\Models\OAuth;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RetrieveAzureGroups implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $azure_api_base_url = env('GRAPH_API_URL');
        $azure_api_resource = '/groups/delta?$select=displayName,members,owners';
        $azure_access_token = OAuth::firstWhere('provider', 'azure')->access_token;
        $client = Http::withToken($azure_access_token)->baseUrl($azure_api_base_url);
        $local_azure_user_ids = AzureUser::select('id')->pluck('id')->toArray();
        $local_azure_group_ids = AzureGroup::select('id')->pluck('id')->toArray();
        $local_azure_memberships = AzureMembership::select(['id', 'azure_group_id', 'azure_user_id'])->get();

        do {
            $response = $client->get($azure_api_resource);
            if ($response->successful()) {
                $date = $response->header('date');
                $link = $response['@odata.nextLink'] ?? $response['@odata.deltaLink'];
                $timestamp = Carbon::createFromFormat(DateTimeInterface::RFC7231, $date);
                $del_group_ids = array_column(array_filter($response['value'], function ($item) {
                    return array_key_exists('@removed', $item);
                }), 'id');
                $add_groups = array_filter($response['value'], function ($item) {
                    return !array_key_exists('@removed', $item);
                });

                $azure_groups = [];
                $add_azure_memberships = [];
                $del_azure_memberships = [];
                foreach ($add_groups as $group) {
                    array_push($azure_groups, [
                        'id' => $group['id'],
                        'name' => array_key_exists('displayName', $group) ?
                            Str::limit($group['displayName']) : null,
                        'synced_at' => $timestamp
                    ]);

                    if (array_key_exists('owners@delta', $group)) {
                        foreach ($group['owners@delta'] as $owner) {
                            if ($owner['@odata.type'] === '#microsoft.graph.user' &&
                                in_array($owner['id'], $local_azure_user_ids)) {
                                if (array_key_exists('@removed', $owner)) {
                                    array_push($del_azure_memberships, [
                                        'azure_group_id' => $group['id'],
                                        'azure_user_id' => $owner['id']
                                    ]);
                                } else {
                                    array_push($add_azure_memberships, [
                                        'azure_group_id' => $group['id'],
                                        'azure_user_id' => $owner['id'],
                                        'is_owner' => true,
                                        'synced_at' => $timestamp
                                    ]);
                                }
                            }
                        }
                    }

                    if (array_key_exists('members@delta', $group)) {
                        foreach ($group['members@delta'] as $member) {
                            if ($member['@odata.type'] === '#microsoft.graph.user' &&
                                in_array($member['id'], $local_azure_user_ids)) {
                                if (array_key_exists('@removed', $member)) {
                                    array_push($del_azure_memberships, [
                                        'azure_group_id' => $group['id'],
                                        'azure_user_id' => $member['id']
                                    ]);
                                } else {
                                    array_push($add_azure_memberships, [
                                        'azure_group_id' => $group['id'],
                                        'azure_user_id' => $member['id'],
                                        'is_owner' => false,
                                        'synced_at' => $timestamp
                                    ]);
                                }
                            }
                        }
                    }
                }

                $del_azure_membership_ids = [];
                foreach ($del_azure_memberships as $del_azure_membership) {
                    $local_azure_membership = $local_azure_memberships
                        ->where('azure_group_id', $del_azure_membership['azure_group_id'])
                        ->where('azure_user_id', $del_azure_membership['azure_user_id'])
                        ->first();
                    if ($local_azure_membership) {
                        array_push($del_azure_membership_ids, $local_azure_membership['id']);
                    }
                }

                AzureMembership::destroy($del_azure_membership_ids);
                AzureGroup::destroy(array_intersect($del_group_ids, $local_azure_group_ids));

                AzureGroup::upsert($azure_groups, ['id'], ['name', 'synced_at']);
                AzureMembership::upsert(
                    $add_azure_memberships,
                    ['azure_group_id', 'azure_user_id'],
                    ['is_owner', 'synced_at']
                );

                if ($link) {
                    $azure_api_resource = Str::after($link, $azure_api_base_url);
                }
            }
        } while ($response->successful() && !empty($response['value']));
    }
}

// TODO: Handle edge case where old deleted users are present in table but not in the response.
// TODO: Refactor to increase re-usability and get rid of so many nested loops.
// TODO: Mechanism to avoid rate limiting.
// TODO: Persist delta links.
