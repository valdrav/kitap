<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Book;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Satışlar';

    protected static ?string $modelLabel = 'Satış';

    protected static ?string $pluralModelLabel = 'Satışlar';

    protected static ?string $navigationGroup = 'Satış Yönetimi';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::query()
            ->whereNotIn('cargo_status', [
                Sale::STATUS_TESLIM_EDILDI,
                Sale::STATUS_IPTAL,
            ])
            ->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Tamamlanmamış kargo sayısı';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['notes'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['region']);
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return ($record->region?->name ?? 'Satış').' — '.$record->sale_date?->format('d.m.Y');
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Durum' => Sale::cargoStatuses()[$record->cargo_status] ?? '-',
            'Tutar' => number_format((float) $record->total_amount, 2, ',', '.').' ₺',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Satış Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('region_id')
                            ->label('Bölge')
                            ->relationship('region', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Bölge Adı')
                                    ->required(),
                                Forms\Components\TextInput::make('code')
                                    ->label('Kod'),
                            ]),
                        Forms\Components\DatePicker::make('sale_date')
                            ->label('Satış Tarihi')
                            ->required()
                            ->default(now())
                            ->displayFormat('d.m.Y')
                            ->native(false),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Kargo Takibi')
                    ->description('Gönderinin güncel durumunu işaretleyin')
                    ->schema([
                        Forms\Components\Select::make('cargo_status')
                            ->label('Kargo Durumu')
                            ->options(Sale::cargoStatuses())
                            ->default(Sale::STATUS_HAZIRLANIYOR)
                            ->required()
                            ->native(false)
                            ->live(),
                        Forms\Components\Select::make('cargo_company')
                            ->label('Kargo Firması')
                            ->options(Sale::cargoCompanies())
                            ->searchable()
                            ->native(false)
                            ->placeholder('Seçiniz'),
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('Takip No')
                            ->maxLength(100)
                            ->placeholder('Örn: 1234567890'),
                        Forms\Components\DateTimePicker::make('status_updated_at')
                            ->label('Durum Güncelleme')
                            ->displayFormat('d.m.Y H:i')
                            ->native(false)
                            ->default(now())
                            ->seconds(false),
                        Forms\Components\Textarea::make('status_note')
                            ->label('Durum Notu')
                            ->rows(2)
                            ->placeholder('Örn: Adreste kimse yoktu, tekrar denenecek')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Satılan Kitaplar')
                    ->description('Bu bölgeye satılan kitapları ve adetlerini girin')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Kalemler')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('book_id')
                                    ->label('Kitap')
                                    ->relationship('book', 'title', fn (Builder $query) => $query->where('is_active', true))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set): void {
                                        $book = Book::find($state);
                                        if ($book) {
                                            $set('unit_price', $book->sale_price);
                                            $set('unit_cost', $book->cost_price);
                                        }
                                    })
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Adet')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set): void {
                                        self::updateSubtotal($get, $set);
                                    }),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Birim Fiyat (₺)')
                                    ->numeric()
                                    ->prefix('₺')
                                    ->required()
                                    ->minValue(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set): void {
                                        self::updateSubtotal($get, $set);
                                    }),
                                Forms\Components\TextInput::make('unit_cost')
                                    ->label('Birim Maliyet (₺)')
                                    ->numeric()
                                    ->prefix('₺')
                                    ->default(0)
                                    ->minValue(0)
                                    ->dehydrated(),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Ara Toplam (₺)')
                                    ->numeric()
                                    ->prefix('₺')
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(0),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel('Kitap Ekle')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => isset($state['book_id'])
                                ? Book::find($state['book_id'])?->title
                                : 'Yeni kalem')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set): void {
                                $items = $get('items') ?? [];
                                $total = collect($items)->sum(fn ($item) => (float) ($item['subtotal'] ?? 0));
                                $set('total_amount', $total);
                            })
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('total_display')
                            ->label('Satış Toplamı')
                            ->content(function (Get $get): string {
                                $items = $get('items') ?? [];
                                $total = collect($items)->sum(function ($item) {
                                    if (isset($item['subtotal'])) {
                                        return (float) $item['subtotal'];
                                    }

                                    return ((float) ($item['quantity'] ?? 0)) * ((float) ($item['unit_price'] ?? 0));
                                });

                                return number_format($total, 2, ',', '.').' ₺';
                            }),
                        Forms\Components\Hidden::make('total_amount')
                            ->default(0)
                            ->dehydrated(),
                        Forms\Components\Hidden::make('total_cost')
                            ->default(0)
                            ->dehydrated(),
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id())
                            ->dehydrated(),
                    ]),
            ]);
    }

    protected static function updateSubtotal(Get $get, Set $set): void
    {
        $qty = (float) ($get('quantity') ?? 0);
        $price = (float) ($get('unit_price') ?? 0);
        $set('subtotal', round($qty * $price, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sale_date')
                    ->label('Tarih')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('region.name')
                    ->label('Bölge')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cargo_status')
                    ->label('Kargo Durumu')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Sale::cargoStatuses()[$state] ?? ($state ?? '-'))
                    ->color(fn (?string $state): string => Sale::cargoStatusColors()[$state] ?? 'gray')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('Takip No')
                    ->copyable()
                    ->placeholder('-')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cargo_company')
                    ->label('Kargo')
                    ->formatStateUsing(fn (?string $state): string => Sale::cargoCompanies()[$state] ?? ($state ?? '-'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Kalem'),
                Tables\Columns\TextColumn::make('items_sum_quantity')
                    ->sum('items', 'quantity')
                    ->label('Toplam Adet'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Gelir')
                    ->money('TRY', locale: 'tr')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Maliyet')
                    ->money('TRY', locale: 'tr')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('profit')
                    ->label('Kar')
                    ->state(fn (Sale $record): float => $record->profit)
                    ->money('TRY', locale: 'tr')
                    ->color(fn (Sale $record): string => $record->profit >= 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('status_updated_at')
                    ->label('Durum Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kaydeden')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Not')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sale_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('cargo_status')
                    ->label('Kargo Durumu')
                    ->options(Sale::cargoStatuses())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('region_id')
                    ->label('Bölge')
                    ->relationship('region', 'name'),
                Tables\Filters\SelectFilter::make('cargo_company')
                    ->label('Kargo Firması')
                    ->options(Sale::cargoCompanies()),
                Tables\Filters\Filter::make('sale_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Başlangıç')->native(false),
                        Forms\Components\DatePicker::make('until')->label('Bitiş')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('sale_date', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('sale_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('updateStatus')
                    ->label('Durum')
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
                    ->fillForm(fn (Sale $record): array => [
                        'cargo_status' => $record->cargo_status,
                        'cargo_company' => $record->cargo_company,
                        'tracking_number' => $record->tracking_number,
                        'status_note' => $record->status_note,
                    ])
                    ->action(function (Sale $record, array $data): void {
                        $record->update([
                            'cargo_status' => $data['cargo_status'],
                            'cargo_company' => $data['cargo_company'] ?? $record->cargo_company,
                            'tracking_number' => $data['tracking_number'] ?? $record->tracking_number,
                            'status_note' => $data['status_note'] ?? $record->status_note,
                            'status_updated_at' => now(),
                        ]);
                    })
                    ->successNotificationTitle('Kargo durumu güncellendi'),
                Tables\Actions\ViewAction::make()->label('Görüntüle'),
                Tables\Actions\EditAction::make()->label('Düzenle'),
                Tables\Actions\DeleteAction::make()->label('Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkUpdateStatus')
                        ->label('Durum Güncelle')
                        ->icon('heroicon-o-truck')
                        ->form([
                            Forms\Components\Select::make('cargo_status')
                                ->label('Yeni Durum')
                                ->options(Sale::cargoStatuses())
                                ->required()
                                ->native(false),
                            Forms\Components\Textarea::make('status_note')
                                ->label('Durum Notu')
                                ->rows(2),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $records->each(function (Sale $record) use ($data): void {
                                $record->update([
                                    'cargo_status' => $data['cargo_status'],
                                    'status_note' => $data['status_note'] ?? $record->status_note,
                                    'status_updated_at' => now(),
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Seçilen satışların durumu güncellendi'),
                    Tables\Actions\DeleteBulkAction::make()->label('Seçilenleri Sil'),
                ]),
            ])
            ->emptyStateHeading('Henüz satış yok')
            ->emptyStateDescription('Bölgelere kitap satışını kaydederek başlayın.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'view' => Pages\ViewSale::route('/{record}'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['region', 'items.book', 'user']);
    }
}
