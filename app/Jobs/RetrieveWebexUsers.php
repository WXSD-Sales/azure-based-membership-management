<?php

namespace App\Jobs;

use App\Models\Cisco\WebexUser;
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

class RetrieveWebexUsers implements ShouldQueue
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
        $webex_api_base_url = env('WEBEX_API_URL');
        $webex_api_resource = '/people';
        $webex_access_token = OAuth::firstWhere('provider', 'webex')->access_token;
        $client = Http::withToken($webex_access_token)->baseUrl($webex_api_base_url);
        $local_ids = WebexUser::select('id')->pluck('id')->toArray();
        $remote_ids = [];
        $link = '';

        do {
            $response = $client->get($webex_api_resource);
            if ($response->successful()) {
                $date = $response->header('date');
                $link = $response->header('link');
                $timestamp = Carbon::createFromFormat(DateTimeInterface::RFC7231, $date);
                $person_items = array_filter($response['items'], function ($item) {
                    return $item['type'] = 'person';
                });

                $remote_ids = array_merge($remote_ids, array_column($person_items, 'id'));
                $webex_users = array_map(function ($person_item) use ($timestamp) {
                    return [
                        'id' => $person_item['id'],
                        'name' => array_key_exists('displayName', $person_item) ?
                            Str::limit($person_item['displayName']) : null,
                        'email' => $person_item['emails'][0],
                        'synced_at' => $timestamp
                    ];
                }, $person_items);

                WebexUser::upsert($webex_users, ['id'], ['name', 'email', 'synced_at']);

                if ($link) {
                    $webex_api_resource = Str::between($link, "<$webex_api_base_url", '>;');
                }
            }
        } while ($response->successful() && $link !== '');

        WebexUser::destroy(array_diff($local_ids, $remote_ids));

        // TODO: Mechanism to avoid rate limiting.
    }
}
