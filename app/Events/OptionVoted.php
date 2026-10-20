<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OptionVoted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $pollId;

    public string $optionId;

    public int $votes;

    public int $totalVotes;

    public float $percentage;

    /**
     * Create a new event instance.
     */
    public function __construct(string $pollId, string $optionId, int $votes, int $totalVotes, float $percentage)
    {
        $this->pollId = $pollId;
        $this->optionId = $optionId;
        $this->votes = $votes;
        $this->totalVotes = $totalVotes;
        $this->percentage = $percentage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('poll.'.$this->pollId),
        ];
    }
}
