<?php

namespace App\Jobs;

use App\Models\Cisco\WebexGroup;
use App\Models\Cisco\WebexMembership;
use App\Models\Cisco\WebexUser;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RetrieveWebexGroups implements ShouldQueue
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
        $this->handleGroups();
        $this->handleMemberships();

        // TODO: Mechanism to avoid rate limiting.
    }

    public function handleGroups(): void
    {
        $webex_api_base_url = env('WEBEX_API_URL');
        $webex_api_resource = '/teams';
        $webex_access_token = env('WEBEX_BOT_TOKEN');
        $client = Http::withToken($webex_access_token)->baseUrl($webex_api_base_url);
        $local_webex_group_ids = WebexGroup::select('id')->pluck('id')->toArray();
        $remote_ids = [];
        $link = '';

        do {
            $response = $client->get($webex_api_resource);
            if ($response->successful()) {
                $date = $response->header('date');
                $link = $response->header('link');
                $timestamp = Carbon::createFromFormat(DateTimeInterface::RFC7231, $date);
                $group_items = $response['items'] ?? [];

                $remote_ids = array_merge($remote_ids, array_column($group_items, 'id'));
                $webex_groups = array_map(function ($group_item) use ($timestamp) {
                    return [
                        'id' => $group_item['id'],
                        'name' => Str::limit($group_item['name']),
                        'synced_at' => $timestamp
                    ];
                }, $group_items);

                WebexGroup::upsert($webex_groups, ['id'], ['name', 'synced_at']);

                if ($link) {
                    $webex_api_resource = Str::between($link, "<$webex_api_base_url", '>;');
                }
            }
        } while ($response->successful() && $link !== '');

        WebexGroup::destroy(array_diff($local_webex_group_ids, $remote_ids));
    }

    public function handleMemberships(): void
    {
        $webex_api_base_url = env('WEBEX_API_URL');
        $webex_api_resource = '/team/memberships';
        $webex_access_token = env('WEBEX_BOT_TOKEN');
        $client = Http::withToken($webex_access_token)->baseUrl($webex_api_base_url);
        $local_webex_user_ids = WebexUser::select('id')->pluck('id')->toArray();
        $local_webex_group_ids = WebexGroup::select('id')->pluck('id')->toArray();
        $local_webex_membership_ids = WebexMembership::select('id')->pluck('id')->toArray();
        $remote_webex_group_ids = [];
        $remote_webex_membership_ids = [];
        $link = '';

        foreach ($local_webex_group_ids as $local_webex_group_id) {
            do {
                $response = $client->get($webex_api_resource . "?teamId=$local_webex_group_id");
                if ($response->successful()) {
                    $date = $response->header('date');
                    $link = $response->header('link');
                    $timestamp = Carbon::createFromFormat(DateTimeInterface::RFC7231, $date);
                    $membership_items = array_filter($response['items'], function ($item)
                    use ($local_webex_group_ids, $local_webex_user_ids) {
                        return in_array($item['personId'], $local_webex_user_ids) &&
                            in_array($item['teamId'], $local_webex_group_ids);
                    });

                    $remote_webex_group_ids = array_merge(
                        $remote_webex_group_ids,
                        array_column($membership_items, 'id')
                    );

                    $webex_memberships = array_map(function ($membership_item) use ($timestamp) {
                        return [
                            'id' => $membership_item['id'],
                            'webex_group_id' => $membership_item['teamId'],
                            'webex_user_id' => $membership_item['personId'],
                            'is_moderator' => (bool)$membership_item['isModerator'],
                            'synced_at' => $timestamp
                        ];
                    }, $membership_items);

                    $remote_webex_membership_ids = array_merge(
                        $remote_webex_membership_ids,
                        array_column($webex_memberships, 'id')
                    );

                    WebexMembership::upsert(
                        $webex_memberships,
                        ['id'],
                        ['webex_group_id', 'webex_user_id', 'is_moderator', 'synced_at']
                    );

                    if ($link) {
                        $webex_api_resource = Str::between($link, "<$webex_api_base_url", '>;');
                    }
                }
            } while ($response->successful() && $link !== '');
        }

        WebexMembership::destroy(array_diff($local_webex_membership_ids, $remote_webex_membership_ids));
    }
}
