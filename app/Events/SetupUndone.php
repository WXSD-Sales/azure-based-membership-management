<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SetupUndone
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    private $client_ip;

    /**
     * @var string
     */
    private $user_agent;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Carbon
     */
    public $timestamp;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, User $user)
    {
        $this->client_ip = $request->getClientIp() ?: "unknown";
        $this->user_agent = $request->userAgent() ?: "unknown";
        $this->user = $user;
        $this->timestamp = now();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('setup');
    }

    public function __toString()
    {
        return config('app.name') .
            " setup was undone using **{$this->user->email}** on **$this->user_agent** user agent" .
            " with **$this->client_ip** ip address on **{$this->timestamp->toCookieString()}**.";
    }
}
