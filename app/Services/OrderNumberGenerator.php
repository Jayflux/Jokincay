<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderNumberGenerator
{
    /**
     * Generate a unique human-friendly order number (e.g. JT-20260911-A8F2).
     */
    public static function generate(): string
    {
        $prefix = 'JT-' . date('Ymd') . '-';

        do {
            $suffix = strtoupper(Str::random(4));
            $orderNumber = $prefix . $suffix;
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}
