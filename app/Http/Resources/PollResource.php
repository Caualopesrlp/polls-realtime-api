<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalVotes = $this->options->sum('votes');

        $options = $this->options->map(function ($option) use ($totalVotes) {
            return [
                'id' => $option->id,
                'poll_id' => $option->poll_id,
                'text' => $option->text,
                'votes' => $option->votes,
                'percentage' => $totalVotes > 0
                    ? round(($option->votes / $totalVotes) * 100, 2)
                    : 0,
                'created_at' => $option->created_at,
                'updated_at' => $option->updated_at,
            ];
        });

        return [
            'id' => $this->id,
            'question' => $this->question,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'total_votes' => $totalVotes,
            'options' => $options,
        ];
    }
}
