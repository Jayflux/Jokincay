<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeOrdersCount = Order::whereIn('status', [
            OrderStatus::WaitingPayment,
            OrderStatus::InProgress,
            OrderStatus::Revision,
        ])->count();

        $completedThisWeekCount = Order::where('status', OrderStatus::Completed)
            ->whereBetween('completed_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->count();

        $verifiedRevenue = Payment::where('status', PaymentStatus::Verified)->sum('amount');

        $pendingPaymentsCount = Payment::where('status', PaymentStatus::PendingVerification)->count();

        $pendingNegoCount = Order::where('status', OrderStatus::PendingNego)->count();

        return [
            Stat::make('Tugas Aktif', $activeOrdersCount)
                ->description('Dalam proses, menunggu bayar, atau revisi')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('primary'),

            Stat::make('Selesai Minggu Ini', $completedThisWeekCount)
                ->description('Tugas rampung minggu ini')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Pemasukan Terverifikasi', 'Rp ' . number_format($verifiedRevenue, 0, ',', '.'))
                ->description('Dari pembayaran yang sudah sah')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Verifikasi Tertunda', $pendingPaymentsCount)
                ->description('Bukti transfer perlu dicek admin')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingPaymentsCount > 0 ? 'danger' : 'gray'),

            Stat::make('Menunggu Nego', $pendingNegoCount)
                ->description('Order baru perlu ditentukan harga')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($pendingNegoCount > 0 ? 'warning' : 'gray'),
        ];
    }
}
