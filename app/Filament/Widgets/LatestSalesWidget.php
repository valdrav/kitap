<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SaleResource;
use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSalesWidget extends BaseWidget
{
    protected static ?string $heading = 'Son Satışlar';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()->with(['region', 'items'])->latest('sale_date')->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('sale_date')
                    ->label('Tarih')
                    ->date('d.m.Y'),
                Tables\Columns\TextColumn::make('region.name')
                    ->label('Bölge')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('cargo_status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Sale::cargoStatuses()[$state] ?? ($state ?? '-'))
                    ->color(fn (?string $state): string => Sale::cargoStatusColors()[$state] ?? 'gray'),
                Tables\Columns\TextColumn::make('items_sum_quantity')
                    ->sum('items', 'quantity')
                    ->label('Adet'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Tutar')
                    ->money('TRY', locale: 'tr')
                    ->weight('bold'),
            ])
            ->actions([
                Tables\Actions\Action::make('updateStatus')
                    ->label('Durum')
                    ->icon('heroicon-m-truck')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\Select::make('cargo_status')
                            ->label('Yeni Durum')
                            ->options(Sale::cargoStatuses())
                            ->required()
                            ->native(false),
                    ])
                    ->fillForm(fn (Sale $record): array => [
                        'cargo_status' => $record->cargo_status,
                    ])
                    ->action(function (Sale $record, array $data): void {
                        $record->update([
                            'cargo_status' => $data['cargo_status'],
                            'status_updated_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('view')
                    ->label('Detay')
                    ->url(fn (Sale $record): string => SaleResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-m-eye'),
            ])
            ->paginated(false);
    }
}
