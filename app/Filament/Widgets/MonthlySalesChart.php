<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlySalesChart extends ChartWidget
{
    protected static ?string $heading = 'Aylık Satış Geliri';

    protected static ?string $description = 'Son 12 aylık gelir trendi';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = Carbon::create($date->year, $date->month, 1)->locale('tr')->translatedFormat('M Y');
            $values[] = (float) Sale::whereMonth('sale_date', $date->month)
                ->whereYear('sale_date', $date->year)
                ->sum('total_amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Gelir (₺)',
                    'data' => $values,
                    'borderColor' => '#0F766E',
                    'backgroundColor' => 'rgba(15, 118, 110, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
