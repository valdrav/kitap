<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ReportsStatsOverview extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $income = (float) Sale::sum('total_amount');
        $saleCost = (float) Sale::sum('total_cost');
        $otherExpenses = (float) Expense::sum('amount');
        $profit = $income - $saleCost - $otherExpenses;
        $booksSold = (int) SaleItem::sum('quantity');
        $delivered = Sale::where('cargo_status', Sale::STATUS_TESLIM_EDILDI)->count();
        $pendingCargo = Sale::whereNotIn('cargo_status', [
            Sale::STATUS_TESLIM_EDILDI,
            Sale::STATUS_IPTAL,
        ])->count();

        return [
            Stat::make('Toplam Gelir', Number::currency($income, 'TRY', 'tr'))
                ->description($booksSold.' kitap satıldı')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Toplam Gider', Number::currency($saleCost + $otherExpenses, 'TRY', 'tr'))
                ->description('Maliyet + diğer giderler')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Net Kar', Number::currency($profit, 'TRY', 'tr'))
                ->description($profit >= 0 ? 'Pozitif bakiye' : 'Zarar durumu')
                ->descriptionIcon($profit >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($profit >= 0 ? 'primary' : 'danger'),
            Stat::make('Kargo Durumu', $delivered.' teslim / '.$pendingCargo.' bekleyen')
                ->description(Book::where('is_active', true)->count().' aktif kitap')
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),
        ];
    }
}
