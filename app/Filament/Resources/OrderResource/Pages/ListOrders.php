<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Resources\Pages\ListRecords;
use Livewire\Component;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Dokumen')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->modalHeading('Export Daftar Pesanan')
                ->modalDescription('Pilih rentang waktu (harian, mingguan, atau bulanan) dan format dokumen yang diinginkan.')
                ->modalSubmitActionLabel('Proses Dokumen')
                ->form([
                    Select::make('period')
                        ->label('Periode Laporan')
                        ->options([
                            'daily' => 'Harian (Hari Ini / Tanggal Tertentu)',
                            'weekly' => 'Mingguan (Minggu Ini)',
                            'monthly' => 'Bulanan (Bulan Ini)',
                            'custom' => 'Kustom (Pilih Rentang Tanggal)',
                        ])
                        ->default('daily')
                        ->required()
                        ->live(),
                    DatePicker::make('date')
                        ->label('Pilih Tanggal')
                        ->default(now())
                        ->visible(fn (Get $get) => $get('period') === 'daily'),
                    DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->default(now()->subDays(7))
                        ->visible(fn (Get $get) => $get('period') === 'custom'),
                    DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->default(now())
                        ->visible(fn (Get $get) => $get('period') === 'custom'),
                    Select::make('format')
                        ->label('Format Dokumen')
                        ->options([
                            'print' => 'Dokumen Cetak / PDF Resmi',
                            'csv' => 'File Excel / CSV',
                        ])
                        ->default('print')
                        ->required(),
                ])
                ->action(function (array $data, Component $livewire) {
                    $url = route('admin.orders.export', $data);
                    if ($data['format'] === 'print') {
                        $livewire->js("window.open('{$url}', '_blank')");
                    } else {
                        return redirect()->away($url);
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
