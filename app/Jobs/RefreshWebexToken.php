<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class RefreshWebexToken implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $auth_url;

    /**
     * @var float|int|string
     */
    private $timestamp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->auth_url = 'https://webexapis.com/v1/access_token';
        $this->timestamp = now()->timestamp;
    }

    public function uniqueId()
    {
        return $this->timestamp;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function handle()
    {
        $webex_oauth = User::where('role', '=', 'superadmin')
            ->latest()
            ->first()
            ->oauths()
            ->where('provider', '=', 'webex')
            ->latest()
            ->first();

        if ($webex_oauth->exists()) {
            $auth_response = Http::asForm()
                ->retry(5, 1000)
                ->post($this->auth_url, [
                    'client_id' => config('services.webex.client_id'),
                    'refresh_token' => $webex_oauth->refresh_token,
                    'grant_type' => 'refresh_token',
                    'client_secret' => config('services.webex.client_secret')
                ]);

            $auth_response->throw();

            $webex_oauth->refresh_token = $auth_response['refresh_token'];
            $webex_oauth->expires_at = $this->timestamp + $auth_response['expires_in'];
            $webex_oauth->access_token = $auth_response['access_token'];

            $webex_oauth->save();
        }
    }
}
