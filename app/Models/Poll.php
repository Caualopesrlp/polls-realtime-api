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

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function isStarted(): bool
    {

        return $this->status != 'nao iniciado';
    }

    public function isOpen(): bool
    {
        if (now() >= $this->start_date && now() <= $this->end_date && $this->status != 'nao iniciado' && $this->status != 'finalizado') {
            return true;
        }

        return false;
    }
}
