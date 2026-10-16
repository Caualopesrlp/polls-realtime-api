<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasUids;

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
}
