<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopBooksChart extends ChartWidget
{
    protected static ?string $heading = 'En Çok Satan Kitaplar';

    protected static ?string $description = 'Adet bazında ilk 8 kitap';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $rows = DB::table('sale_items')
            ->join('books', 'books.id', '=', 'sale_items.book_id')
            ->select('books.title', DB::raw('SUM(sale_items.quantity) as total_qty'))
            ->groupBy('books.id', 'books.title')
            ->orderByDesc('total_qty')
            ->limit(8)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Satılan Adet',
                    'data' => $rows->pluck('total_qty')->map(fn ($v) => (int) $v)->all(),
                    'backgroundColor' => '#0F766E',
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $rows->pluck('title')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
