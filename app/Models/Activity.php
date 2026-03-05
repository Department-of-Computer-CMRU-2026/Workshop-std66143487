<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'speaker',
        'location',
        'total_seats',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class , 'registrations');
    }

    public function getRemainingSeatsAttribute(): int
    {
        return $this->total_seats - $this->registrations()->count();
    }

    public function getIsFullAttribute(): bool
    {
        return $this->remaining_seats <= 0;
    }
}
