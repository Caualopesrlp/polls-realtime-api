<?php

namespace App\Models;

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
        'status' => 'nao_iniciado',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function isStarted(): bool
    {
        return $this->status !== 'nao_iniciado';
    }

    public function isOpen(): bool
    {
        return now() >= $this->start_date
            && now() <= $this->end_date
            && $this->status !== 'nao_iniciado'
            && $this->status !== 'finalizado';
    }
}
