<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class FinanceStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $income = (float) Sale::sum('total_amount');
        $saleCost = (float) Sale::sum('total_cost');
        $expenses = (float) Expense::sum('amount');
        $totalExpense = $saleCost + $expenses;
        $profit = $income - $totalExpense;
        $booksSold = (int) SaleItem::sum('quantity');
        $saleCount = Sale::count();

        $thisMonthIncome = (float) Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->sum('total_amount');

        $lastMonthIncome = (float) Sale::whereMonth('sale_date', now()->subMonth()->month)
            ->whereYear('sale_date', now()->subMonth()->year)
            ->sum('total_amount');

        $trend = $lastMonthIncome > 0
            ? round((($thisMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100, 1)
            : ($thisMonthIncome > 0 ? 100 : 0);

        return [
            Stat::make('Toplam Gelir', Number::currency($income, 'TRY', 'tr'))
                ->description('Bu ay: '.Number::currency($thisMonthIncome, 'TRY', 'tr'))
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color('success')
                ->chart($this->lastSixMonthsTotals('income')),
            Stat::make('Toplam Gider', Number::currency($totalExpense, 'TRY', 'tr'))
                ->description('Satış maliyeti + diğer giderler')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart($this->lastSixMonthsTotals('expense')),
            Stat::make('Net Kar', Number::currency($profit, 'TRY', 'tr'))
                ->description($profit >= 0 ? 'Pozitif bakiye' : 'Zarar durumu')
                ->descriptionIcon($profit >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->color($profit >= 0 ? 'primary' : 'danger'),
            Stat::make('Satılan Kitap', Number::format($booksSold, locale: 'tr').' adet')
                ->description($saleCount.' satış kaydı')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
        ];
    }

    protected function lastSixMonthsTotals(string $type): array
    {
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            if ($type === 'income') {
                $data[] = (float) Sale::whereMonth('sale_date', $date->month)
                    ->whereYear('sale_date', $date->year)
                    ->sum('total_amount');
            } else {
                $saleCost = (float) Sale::whereMonth('sale_date', $date->month)
                    ->whereYear('sale_date', $date->year)
                    ->sum('total_cost');
                $other = (float) Expense::whereMonth('expense_date', $date->month)
                    ->whereYear('expense_date', $date->year)
                    ->sum('amount');
                $data[] = $saleCost + $other;
            }
        }

        return $data;
    }
}
