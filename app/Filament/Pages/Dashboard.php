<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\FinanceStatsOverview;
use App\Filament\Widgets\LatestSalesWidget;
use App\Filament\Widgets\MonthlySalesChart;
use App\Filament\Widgets\RegionSalesChart;
use App\Filament\Widgets\TopBooksChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Panel';

    protected static ?string $title = 'Kontrol Paneli';

    public function getWidgets(): array
    {
        return [
            FinanceStatsOverview::class,
            MonthlySalesChart::class,
            RegionSalesChart::class,
            TopBooksChart::class,
            LatestSalesWidget::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return 2;
    }
}
