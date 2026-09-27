<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pembayaran';
    protected static ?string $modelLabel = 'Pembayaran';
    protected static ?string $pluralModelLabel = 'Daftar Pembayaran';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->label('Pesanan')
                    ->relationship('order', 'order_number')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Bayar')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status Pembayaran')
                    ->options(\App\Enums\PaymentStatus::class)
                    ->required(),
                Forms\Components\FileUpload::make('proof_path')
                    ->label('File Bukti Pembayaran')
                    ->disk('local')
                    ->directory('payment-proofs')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                    ->maxSize(5120)
                    ->openable()
                    ->downloadable()
                    ->previewable()
                    ->columnSpanFull()
                    ->helperText('Unggah berkas bukti transfer (JPG, PNG, atau PDF maks. 5MB)'),
                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Alasan Penolakan (bila ada)')
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('No. Order')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Payment $record): string => $record->order?->customer?->name ?? '-'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('uploaded_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Diverifikasi')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Verifikator')
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(\App\Enums\PaymentStatus::class),
            ])
            ->actions([
                Tables\Actions\Action::make('view_proof')
                    ->label('Bukti')
                    ->icon('heroicon-m-document-magnifying-glass')
                    ->color('info')
                    ->url(fn (Payment $record): string => route('admin.payments.proof', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Payment $record): bool => !empty($record->proof_path)),
                Tables\Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Bukti Pembayaran')
                    ->modalDescription('Apakah Anda yakin pembayaran ini valid? Status order terkait akan otomatis menjadi "Sedang Dikerjakan".')
                    ->action(function (Payment $record): void {
                        app(\App\Services\PaymentService::class)->verifyPayment($record, auth()->user());
                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran Terverifikasi!')
                            ->body('Status order telah diperbarui menjadi Sedang Dikerjakan.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Payment $record): bool => $record->status !== \App\Enums\PaymentStatus::Verified),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->placeholder('Contoh: Nominal transfer tidak sesuai / bukti tidak terbaca')
                            ->required(),
                    ])
                    ->action(function (Payment $record, array $data): void {
                        app(\App\Services\PaymentService::class)->rejectPayment($record, $data['rejection_reason']);
                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran Ditolak')
                            ->body('Alasan penolakan telah disimpan.')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn (Payment $record): bool => $record->status === \App\Enums\PaymentStatus::PendingVerification),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
