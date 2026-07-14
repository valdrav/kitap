<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Giderler';

    protected static ?string $modelLabel = 'Gider';

    protected static ?string $pluralModelLabel = 'Giderler';

    protected static ?string $navigationGroup = 'Finans';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'category', 'notes'];
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Tutar' => number_format((float) $record->amount, 2, ',', '.').' ₺',
            'Tarih' => $record->expense_date?->format('d.m.Y') ?? '-',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Gider Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options(Expense::categories())
                            ->searchable()
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('amount')
                            ->label('Tutar (₺)')
                            ->numeric()
                            ->prefix('₺')
                            ->required()
                            ->minValue(0)
                            ->step(0.01),
                        Forms\Components\DatePicker::make('expense_date')
                            ->label('Gider Tarihi')
                            ->required()
                            ->default(now())
                            ->displayFormat('d.m.Y')
                            ->native(false),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->label('Tarih')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Expense::categories()[$state] ?? ($state ?? '-'))
                    ->color('gray'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Tutar')
                    ->money('TRY', locale: 'tr')
                    ->sortable()
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Not')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('expense_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(Expense::categories()),
                Tables\Filters\Filter::make('expense_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Başlangıç')->native(false),
                        Forms\Components\DatePicker::make('until')->label('Bitiş')->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('expense_date', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('expense_date', '<=', $date));
                    }),
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
            ->emptyStateHeading('Henüz gider yok')
            ->emptyStateDescription('Dernek giderlerini buradan kaydedin.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
