<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Kitaplar';

    protected static ?string $modelLabel = 'Kitap';

    protected static ?string $pluralModelLabel = 'Kitaplar';

    protected static ?string $navigationGroup = 'Ürünler';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationBadge(): ?string
    {
        $lowStock = static::getModel()::query()
            ->where('is_active', true)
            ->where('stock', '<', 20)
            ->count();

        return $lowStock > 0 ? (string) $lowStock : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Düşük stoklu kitap sayısı';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'author', 'isbn'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Yazar' => $record->author ?: '-',
            'Fiyat' => number_format((float) $record->sale_price, 2, ',', '.').' ₺',
            'Stok' => (string) $record->stock,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kapak Görseli')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Kitap Kapağı')
                            ->image()
                            ->directory('book-covers')
                            ->disk('public')
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->maxSize(4096)
                            ->helperText('Önerilen: kare veya dikey kapak görseli (maks. 4 MB)'),
                    ]),
                Forms\Components\Section::make('Kitap Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Kitap Adı')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('author')
                            ->label('Yazar')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('isbn')
                            ->label('ISBN')
                            ->maxLength(50),
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Fiyat ve Stok')
                    ->schema([
                        Forms\Components\TextInput::make('sale_price')
                            ->label('Satış Fiyatı (₺)')
                            ->numeric()
                            ->prefix('₺')
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01),
                        Forms\Components\TextInput::make('cost_price')
                            ->label('Maliyet Fiyatı (₺)')
                            ->numeric()
                            ->prefix('₺')
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->helperText('Kar hesaplaması için kullanılır'),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok Adedi')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Satışa Açık')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Kapak')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(url('/images/book-placeholder.svg')),
                Tables\Columns\TextColumn::make('title')
                    ->label('Kitap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Book $record): ?string => $record->author),
                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Satış Fiyatı')
                    ->money('TRY', locale: 'tr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Maliyet')
                    ->money('TRY', locale: 'tr')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state < 20 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktiflik'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Düşük stok (< 20)')
                    ->query(fn ($query) => $query->where('stock', '<', 20)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Düzenle'),
                Tables\Actions\DeleteAction::make()->label('Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Seçilenleri Sil'),
                ]),
            ])
            ->defaultSort('title')
            ->emptyStateHeading('Henüz kitap yok')
            ->emptyStateDescription('Satılacak kitapları ekleyerek başlayın.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
