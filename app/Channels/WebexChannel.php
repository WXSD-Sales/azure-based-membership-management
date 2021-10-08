<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class WebexChannel
{
    /**
     * @var string
     */
    private $client;

    public function __construct($token = null)
    {
        $this->client = Http::withToken((string)env('WEBEX_BOT_TOKEN'))
            ->baseUrl((string)env('WEBEX_API_URL'))
            ->retry(5, 180);
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $this->client
            ->post('/messages', [
                'toPersonEmail' => $notifiable->email,
                'markdown' => $notification->message
            ]);
    }
}
