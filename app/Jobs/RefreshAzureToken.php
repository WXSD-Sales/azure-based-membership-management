<?php

namespace App\Jobs;

use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class RefreshAzureToken implements ShouldQueue, ShouldBeUnique
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
        $this->auth_url = 'https://login.microsoftonline.com/' .
            config('services.azure.tenant') .
            '/oauth2/v2.0/token';
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
        $azure_oauth = User::where('role', '=', 'superadmin')
            ->latest()
            ->first()
            ->oauths()
            ->where('provider', '=', 'azure')
            ->latest()
            ->first();

        if ($azure_oauth->exists()) {
            $auth_response = Http::asForm()
                ->retry(5, 1000)
                ->post($this->auth_url, [
                    'client_id' => config('services.azure.client_id'),
                    'scope' => implode(" ", RegisterController::$azureScopes),
                    'refresh_token' => $azure_oauth->refresh_token,
                    'redirect_uri' => url(config('services.azure.redirect')),
                    'grant_type' => 'refresh_token',
                    'client_secret' => config('services.azure.client_secret')
                ]);

            $auth_response->throw();

            $azure_oauth->refresh_token = $auth_response['refresh_token'];
            $azure_oauth->expires_at = $this->timestamp + $auth_response['expires_in'];
            $azure_oauth->access_token = $auth_response['access_token'];

            $azure_oauth->save();
        }
    }
}
