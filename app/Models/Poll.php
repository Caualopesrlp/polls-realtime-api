<?php

namespace App\Models;

use App\Enums\PollStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasUuids;

    protected $fillable = [
        'question',
        'status',
        'start_date',
        'end_date',
    ];

    protected $attributes = [
        'status' => PollStatus::NAO_INICIADO->value,
    ];

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function isStarted(): bool
    {
        return $this->status !== PollStatus::NAO_INICIADO;
    }

    public function isOpen(): bool
    {
        return now() >= $this->start_date
            && now() <= $this->end_date
            && $this->status !== PollStatus::NAO_INICIADO
            && $this->status !== PollStatus::FINALIZADO;
    }

    protected function casts(): array
    {
        return [
            'status' => PollStatus::class,
        ];
    }
}
