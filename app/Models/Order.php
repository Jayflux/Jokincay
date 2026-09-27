<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'task_type',
        'description',
        'attachment_path',
        'attachment_name',
        'price',
        'deadline',
        'status',
        'progress',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'price' => 'decimal:2',
            'progress' => 'integer',
            'deadline' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Order $order) {
            // Automatically create connected Payment record with Unpaid status
            $order->payments()->create([
                'amount' => $order->price ?? 0,
                'status' => \App\Enums\PaymentStatus::Unpaid,
            ]);
        });

        static::updated(function (Order $order) {
            // Keep connected unpaid payment amount in sync when order price is updated
            if ($order->wasChanged('price') && $order->price !== null) {
                $order->payments()
                    ->where('status', \App\Enums\PaymentStatus::Unpaid)
                    ->update(['amount' => $order->price]);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}
