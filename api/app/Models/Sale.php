<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;

    public const COMMISSION_RATE = 0.085;

    protected $fillable = ['seller_id', 'amount', 'sold_at'];

    protected $casts = [
        'amount'  => 'decimal:2',
        'sold_at' => 'date',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function getCommissionAttribute(): float
    {
        return (float) $this->amount * self::COMMISSION_RATE;
    }

     public function scopeOfSeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeSoldBetween($query, ?string $from, ?string $to)
    {
        if ($from && $to)    return $query->whereBetween('sold_at', [$from, $to]);
        if ($from)           return $query->whereDate('sold_at', '>=', $from);
        if ($to)             return $query->whereDate('sold_at', '<=', $to);
        return $query;
    }
}
