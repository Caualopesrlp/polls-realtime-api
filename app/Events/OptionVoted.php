<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OptionVoted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $pollId;
    public string $optionId;
    public int $votes;

    /**
     * Create a new event instance.
     */
    public function __construct(string $pollId, string $optionId, int $votes)
    {
        $this->pollId = $pollId;
        $this->optionId = $optionId;
        $this->votes = $votes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('poll.' . $this->pollId),
        ];
    }
}
