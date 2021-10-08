<?php

namespace App\Jobs;

use App\Models\Microsoft\AzureUser;
use App\Models\OAuth;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RetrieveAzureUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var PendingRequest
     */
    private $client;

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
        $azure_api_resource = '/users/delta?$select=id,displayName,userPrincipalName';
        $azure_access_token = OAuth::firstWhere('provider', 'azure')->access_token;
        $client = Http::withToken($azure_access_token)->baseUrl($azure_api_base_url);
        $local_ids = AzureUser::select('id')->pluck('id')->toArray();

        do {
            $response = $client->get($azure_api_resource);
            if ($response->successful()) {
                $date = $response->header('date');
                $link = $response['@odata.nextLink'] ?? $response['@odata.deltaLink'];
                $timestamp = Carbon::createFromFormat(DateTimeInterface::RFC7231, $date);
                $add_users = array_filter($response['value'], function ($item) {
                    return !array_key_exists('@removed', $item);
                });
                $del_users = array_filter($response['value'], function ($item) {
                    return array_key_exists('@removed', $item);
                });
                $del_user_ids = array_map(function ($item) {
                    return $item['id'];
                }, $del_users);

                AzureUser::destroy(array_intersect($del_user_ids, $local_ids));

                $azure_users = array_map(function ($user) use ($timestamp) {
                    return [
                        'id' => $user['id'],
                        'name' => array_key_exists('displayName', $user) ?
                            Str::limit($user['displayName']) : null,
                        'email' => $user['userPrincipalName'],
                        'synced_at' => $timestamp
                    ];
                }, $add_users);

                AzureUser::upsert($azure_users, ['id'], ['name', 'email', 'synced_at']);

                if ($link) {
                    $azure_api_resource = Str::after($link, $azure_api_base_url);
                }
            }
        } while ($response->successful() && !empty($response['value']));

        // TODO: Handle edge case where old delted users are present in table
        //       but not using delta link.
        // TODO: Mechanism to avoid rate limiting.
    }
}
