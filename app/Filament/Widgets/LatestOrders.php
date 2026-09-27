<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Pesanan Terbaru & Perlu Perhatian')
            ->query(
                Order::query()->with('customer')->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Order')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->description(fn (Order $record): string => $record->customer?->whatsapp ?? '-'),
                Tables\Columns\TextColumn::make('task_type')
                    ->label('Jenis Tugas')
                    ->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->placeholder('Menunggu Nego'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progres')
                    ->formatStateUsing(fn (int $state): string => "{$state}%"),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Masuk')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_order')
                    ->label('Kelola')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (Order $record): string => \App\Filament\Resources\OrderResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
