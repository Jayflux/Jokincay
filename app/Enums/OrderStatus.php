<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel, HasColor
{
    case PendingNego = 'pending_nego';
    case WaitingPayment = 'waiting_payment';
    case InProgress = 'in_progress';
    case Revision = 'revision';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PendingNego => 'Menunggu Negosiasi',
            self::WaitingPayment => 'Menunggu Pembayaran',
            self::InProgress => 'Sedang Dikerjakan',
            self::Revision => 'Revisi',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PendingNego => 'warning',
            self::WaitingPayment => 'info',
            self::InProgress => 'primary',
            self::Revision => 'danger',
            self::Completed => 'success',
            self::Cancelled => 'gray',
        };
    }
}
