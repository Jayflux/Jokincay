<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?string $modelLabel = 'Pesanan';
    protected static ?string $pluralModelLabel = 'Daftar Pesanan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pesanan')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Nomor Order')
                            ->default(fn () => \App\Services\OrderNumberGenerator::generate())
                            ->required()
                            ->readOnly(),
                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('task_type')
                            ->label('Jenis Tugas')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Textarea::make('description')
                            ->label('Detail Tugas')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('attachment_info')
                            ->label('Berkas Lampiran Tugas')
                            ->content(fn (?Order $record): \Illuminate\Support\HtmlString => 
                                $record && $record->attachment_path
                                    ? new \Illuminate\Support\HtmlString('<a href="' . route('admin.orders.attachment', $record) . '" target="_blank" class="inline-flex items-center gap-1.5 font-bold text-indigo-600 hover:underline"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg> Unduh: ' . e($record->attachment_name ?? 'Berkas Tugas') . '</a>')
                                    : new \Illuminate\Support\HtmlString('<span class="text-slate-400">Tidak ada berkas terlampir</span>')
                            )
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Harga, Status & Progres')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Harga (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->label('Status Order')
                            ->options(\App\Enums\OrderStatus::class)
                            ->required(),
                        Forms\Components\TextInput::make('progress')
                            ->label('Progres (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(0),
                        Forms\Components\DateTimePicker::make('deadline')
                            ->label('Deadline')
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Waktu Selesai')
                            ->nullable(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Internal Admin')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Order')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Order $record): string => $record->customer?->whatsapp ?? '-'),
                Tables\Columns\TextColumn::make('task_type')
                    ->label('Jenis Tugas')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\IconColumn::make('attachment_path')
                    ->label('Berkas')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('primary')
                    ->falseColor('gray')
                    ->tooltip(fn (Order $record): ?string => $record->attachment_name),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->placeholder('Belum diset'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progres')
                    ->formatStateUsing(fn (int $state): string => "{$state}%")
                    ->sortable(),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(\App\Enums\OrderStatus::class),
            ])
            ->actions([
                Tables\Actions\Action::make('download_file')
                    ->label('Berkas')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('info')
                    ->url(fn (Order $record): string => route('admin.orders.attachment', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Order $record): bool => !empty($record->attachment_path)),
                Tables\Actions\Action::make('chat_wa')
                    ->label('WA')
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Order $record): string => $record->customer ? app(\App\Services\WhatsAppService::class)->customerChatUrl($record->customer, "Halo {$record->customer->name}, kami dari Joki Tugas terkait pesanan #{$record->order_number}.") : '#')
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('upload_payment')
                    ->label('Input Bayar')
                    ->icon('heroicon-m-credit-card')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Nominal Pembayaran (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(fn (Order $record): float => (float) ($record->price ?? 0))
                            ->required(),
                        Forms\Components\FileUpload::make('proof')
                            ->label('File Bukti Transfer')
                            ->disk('local')
                            ->directory('payment-proofs')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->maxSize(5120)
                            ->required()
                            ->helperText('Unggah foto/screenshot/PDF bukti pembayaran'),
                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->options(\App\Enums\PaymentStatus::class)
                            ->default(\App\Enums\PaymentStatus::Verified)
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $payment = $record->payments()->where('status', \App\Enums\PaymentStatus::Unpaid)->first();

                        $payData = [
                            'amount' => $data['amount'],
                            'proof_path' => $data['proof'],
                            'status' => $data['status'],
                            'uploaded_at' => now(),
                            'verified_at' => $data['status'] === \App\Enums\PaymentStatus::Verified ? now() : null,
                            'verified_by' => $data['status'] === \App\Enums\PaymentStatus::Verified ? auth()->id() : null,
                        ];

                        if ($payment) {
                            $payment->update($payData);
                        } else {
                            $record->payments()->create($payData);
                        }

                        if ($data['status'] === \App\Enums\PaymentStatus::Verified) {
                            if (in_array($record->status, [\App\Enums\OrderStatus::PendingNego, \App\Enums\OrderStatus::WaitingPayment])) {
                                $record->update([
                                    'status' => \App\Enums\OrderStatus::InProgress,
                                    'progress' => max($record->progress, 20),
                                ]);
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran berhasil dicatat!')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('set_price')
                    ->label('Set Harga')
                    ->icon('heroicon-m-banknotes')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('price')
                            ->label('Harga Disepakati (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $updateData = ['price' => $data['price']];
                        if ($record->status === \App\Enums\OrderStatus::PendingNego) {
                            $updateData['status'] = \App\Enums\OrderStatus::WaitingPayment;
                        }
                        $record->update($updateData);
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
