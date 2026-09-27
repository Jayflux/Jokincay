<?php

namespace App\Services;

class WhatsAppNormalizer
{
    /**
     * Normalize WhatsApp phone number into canonical international format (628...).
     */
    public static function normalize(?string $number): string
    {
        if (empty($number)) {
            return '';
        }

        // Strip everything except digits
        $digits = preg_replace('/\D+/', '', $number);

        // Convert 08... to 628...
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        // If user typed 8..., prepend 62
        if (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }
}
