<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = [
        'eo_id',
        'event_id',
        'gross_amount',
        'platform_commission',
        'net_amount',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'status',
        'proof_path',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    public function eo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'eo_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}