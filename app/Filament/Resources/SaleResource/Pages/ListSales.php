<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Sale;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Yeni Satış'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Tümü')
                ->badge(Sale::query()->count()),
        ];

        foreach (Sale::cargoStatuses() as $key => $label) {
            $tabs[$key] = Tab::make($label)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('cargo_status', $key))
                ->badge(Sale::query()->where('cargo_status', $key)->count())
                ->badgeColor(Sale::cargoStatusColors()[$key] ?? 'gray');
        }

        return $tabs;
    }
}
