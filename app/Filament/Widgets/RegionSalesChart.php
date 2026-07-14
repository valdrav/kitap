<?php

namespace App\Filament\Widgets;

use App\Models\Region;
use Filament\Widgets\ChartWidget;

class RegionSalesChart extends ChartWidget
{
    protected static ?string $heading = 'Bölgelere Göre Satış';

    protected static ?string $description = 'Bölge bazlı toplam gelir dağılımı';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $regions = Region::query()
            ->withSum('sales', 'total_amount')
            ->orderByDesc('sales_sum_total_amount')
            ->get()
            ->filter(fn (Region $region) => (float) $region->sales_sum_total_amount > 0);

        $colors = [
            '#0F766E', '#0369A1', '#4F46E5', '#B45309', '#BE123C',
            '#15803D', '#7C3AED', '#0E7490', '#C2410C', '#334155',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Gelir',
                    'data' => $regions->pluck('sales_sum_total_amount')->map(fn ($v) => (float) $v)->values()->all(),
                    'backgroundColor' => $regions->values()->map(fn ($r, $i) => $colors[$i % count($colors)])->all(),
                ],
            ],
            'labels' => $regions->pluck('name')->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
