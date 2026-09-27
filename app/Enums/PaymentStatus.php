<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel, HasColor
{
    case Unpaid = 'unpaid';
    case PendingVerification = 'pending_verification';
    case Verified = 'verified';
    case Rejected = 'rejected';
    case Refunded = 'refunded';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Unpaid => 'Belum Dibayar',
            self::PendingVerification => 'Menunggu Verifikasi',
            self::Verified => 'Terverifikasi',
            self::Rejected => 'Ditolak',
            self::Refunded => 'Dikembalikan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Unpaid => 'gray',
            self::PendingVerification => 'warning',
            self::Verified => 'success',
            self::Rejected => 'danger',
            self::Refunded => 'info',
        };
    }
}
