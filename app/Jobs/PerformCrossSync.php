<?php

namespace App\Jobs;

use App\Models\Cisco\WebexGroup;
use App\Models\Cisco\WebexMembership;
use App\Models\Cisco\WebexUser;
use App\Models\Microsoft\AzureGroup;
use App\Models\SyncMapping;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class PerformCrossSync implements ShouldQueue
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
     *
     * @throws Throwable
     */
    public function handle()
    {

        $webex_api_base_url = env('WEBEX_API_URL');
        $webex_access_token = env('WEBEX_BOT_TOKEN');
        $client = Http::withToken($webex_access_token)->baseUrl($webex_api_base_url);

        $webex_user_emails = WebexUser::select('email')->pluck('email')->toArray();
        $azure_gropus = AzureGroup::get();
        $sync_mappings = SyncMapping::get();


        foreach ($azure_gropus as $azure_group) {
            $sync_mapping = $sync_mappings
                ->firstWhere('azure_group_id', '=', $azure_group->id);

            // webex group exists
            if (isset($sync_mapping)) {
                Log::info("mapping exists!");
                $webex_api_resource = '/team/memberships';
                $azure_group_user_emails = $azure_group
                    ->users()
                    ->select('email')
                    ->pluck('email')
                    ->toArray();
                $webex_group_user_emails = WebexGroup::firstWhere(
                    'id', '=', $sync_mapping->webex_group_id
                )
                    ->users()
                    ->select('email')
                    ->pluck('email')
                    ->toArray();
                $add_user_emails = array_diff($azure_group_user_emails, $webex_group_user_emails);
                foreach ($add_user_emails as $add_user_email) {
                    if (in_array($add_user_email, $webex_user_emails)) {
                        Log::info("Adding $add_user_email to $sync_mapping->webex_group_id");
                        $client->post($webex_api_resource, [
                            'teamId' => $sync_mapping->webex_group_id,
                            'personEmail' => $add_user_email
                        ]);
                    }
                }
            } else {
                Log::info("mapping doesn't exists!");
                DB::transaction(function () use ($webex_user_emails, $azure_group, $client) {
                    $webex_api_resource = '/teams';
                    $teams_response = $client->post($webex_api_resource, [
                        'name' => $azure_group->name
                    ]);

                    if ($teams_response->successful()) {
                        $teamId = $teams_response['id'];
                        $date = $teams_response->header('date');
                        $timestamp = Carbon::createFromFormat(DateTimeInterface::RFC7231, $date);
                        $webex_group = $this->upsertWebexGroup($teamId, $teams_response['name'], $timestamp);
                        SyncMapping::create([
                            'azure_group_id' => $azure_group->id,
                            'webex_group_id' => $teams_response['id']
                        ]);

                        $webex_api_resource = '/team/memberships';
                        $azure_group_user_emails = $azure_group
                            ->users()
                            ->select('email')
                            ->pluck('email')
                            ->toArray();

                        foreach ($azure_group_user_emails as $azure_group_user_email) {
                            if (in_array($azure_group_user_email, $webex_user_emails)) {
                                $response = $client->post($webex_api_resource, [
                                    'teamId' => $teamId,
                                    'personEmail' => $azure_group_user_email
                                ]);

                                $date = $response->header('date');
                                $timestamp = Carbon::createFromFormat(DateTimeInterface::RFC7231, $date);

                                if ($response->successful()) {
                                    $this->upsertWebexMembership($response, $webex_group, $timestamp);
                                }
                            }
                        }
                    }
                });
            }
        }
    }

    //TODO: Use Relations.
    //TODO: Use Membership Model directly.
    //TODO: Validate before using response blindly

    /**
     * @param $teamId
     * @param $name
     * @param $timestamp
     * @return WebexGroup|Model
     */
    public function upsertWebexGroup($teamId, $name, $timestamp)
    {
        return WebexGroup::updateOrCreate(['id' => $teamId], [
            'name' => Str::limit($name),
            'synced_at' => $timestamp
        ]);
    }

    /**
     * @param $response
     * @param $webex_group
     * @param $timestamp
     */
    public function upsertWebexMembership($response, $webex_group, $timestamp): void
    {
        WebexMembership::updateOrCreate(['id' => $response['id']], [
            'webex_group_id' => $webex_group->id,
            'webex_user_id' => $response['personId'],
            'synced_at' => $timestamp
        ]);
    }
}
