<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const MAX_TICKETS_PER_ORDER = 5;

    protected $fillable = [
        'order_code',
        'user_id',
        'event_id',
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'total_amount',
        'platform_fee',
        'payment_method',
        'payment_status',
        'snap_token',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}