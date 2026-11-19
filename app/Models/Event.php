<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    protected $fillable = [
        'eo_id',
        'title',
        'slug',
        'category',
        'description',
        'lineup',
        'event_date',
        'start_time',
        'end_time',
        'open_gate',
        'venue_name',
        'venue_address',
        'banner_path',
        'poster',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'lineup' => 'array',
            'event_date' => 'date',
        ];
    }

    public function eo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'eo_id');
    }

    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function payout(): HasOne
    {
        return $this->hasOne(Payout::class);
    }
}