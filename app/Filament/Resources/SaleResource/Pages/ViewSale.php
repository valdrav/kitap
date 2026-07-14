<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Sale;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSale extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    protected static ?string $title = 'Satış Detayı';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('updateStatus')
                ->label('Durum Güncelle')
                ->icon('heroicon-o-truck')
                ->color('info')
                ->form([
                    Forms\Components\Select::make('cargo_status')
                        ->label('Yeni Durum')
                        ->options(Sale::cargoStatuses())
                        ->required()
                        ->native(false),
                    Forms\Components\Select::make('cargo_company')
                        ->label('Kargo Firması')
                        ->options(Sale::cargoCompanies())
                        ->native(false),
                    Forms\Components\TextInput::make('tracking_number')
                        ->label('Takip No'),
                    Forms\Components\Textarea::make('status_note')
                        ->label('Durum Notu')
                        ->rows(2),
                ])
                ->fillForm(fn (): array => [
                    'cargo_status' => $this->record->cargo_status,
                    'cargo_company' => $this->record->cargo_company,
                    'tracking_number' => $this->record->tracking_number,
                    'status_note' => $this->record->status_note,
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'cargo_status' => $data['cargo_status'],
                        'cargo_company' => $data['cargo_company'] ?? $this->record->cargo_company,
                        'tracking_number' => $data['tracking_number'] ?? $this->record->tracking_number,
                        'status_note' => $data['status_note'] ?? $this->record->status_note,
                        'status_updated_at' => now(),
                    ]);
                })
                ->successNotificationTitle('Kargo durumu güncellendi'),
            Actions\EditAction::make()->label('Düzenle'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Satış Özeti')
                    ->schema([
                        Infolists\Components\TextEntry::make('sale_date')
                            ->label('Tarih')
                            ->date('d.m.Y'),
                        Infolists\Components\TextEntry::make('region.name')
                            ->label('Bölge')
                            ->badge()
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('Toplam Gelir')
                            ->money('TRY', locale: 'tr'),
                        Infolists\Components\TextEntry::make('total_cost')
                            ->label('Toplam Maliyet')
                            ->money('TRY', locale: 'tr'),
                        Infolists\Components\TextEntry::make('profit')
                            ->label('Kar')
                            ->state(fn ($record) => $record->profit)
                            ->money('TRY', locale: 'tr'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Kaydeden'),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notlar')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make('Kargo Takibi')
                    ->schema([
                        Infolists\Components\TextEntry::make('cargo_status')
                            ->label('Durum')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => Sale::cargoStatuses()[$state] ?? ($state ?? '-'))
                            ->color(fn (?string $state): string => Sale::cargoStatusColors()[$state] ?? 'gray'),
                        Infolists\Components\TextEntry::make('cargo_company')
                            ->label('Kargo Firması')
                            ->formatStateUsing(fn (?string $state): string => Sale::cargoCompanies()[$state] ?? ($state ?? '-')),
                        Infolists\Components\TextEntry::make('tracking_number')
                            ->label('Takip No')
                            ->copyable()
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('status_updated_at')
                            ->label('Son Güncelleme')
                            ->dateTime('d.m.Y H:i')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('status_note')
                            ->label('Durum Notu')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Satılan Kitaplar')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('book.title')
                                    ->label('Kitap'),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label('Adet'),
                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label('Birim Fiyat')
                                    ->money('TRY', locale: 'tr'),
                                Infolists\Components\TextEntry::make('subtotal')
                                    ->label('Ara Toplam')
                                    ->money('TRY', locale: 'tr'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }
}
