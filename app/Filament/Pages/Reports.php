<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ReportsStatsOverview;
use App\Models\Book;
use App\Models\Expense;
use App\Models\Region;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string $view = 'filament.pages.reports';

    protected static ?string $navigationLabel = 'Raporlar';

    protected static ?string $title = 'Detaylı Raporlar';

    protected static ?string $navigationGroup = 'Finans';

    protected static ?int $navigationSort = 2;

    protected static ?string $pollingInterval = null;

    public function getSubheading(): ?string
    {
        return 'Bölge, kitap, kargo ve gider analizlerinin özeti';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ReportsStatsOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'byRegion' => $this->regionReport(),
            'byBook' => $this->bookReport(),
            'byCargo' => $this->cargoReport(),
            'byExpenseCategory' => $this->expenseReport(),
            'meta' => [
                'active_books' => Book::where('is_active', true)->count(),
                'active_regions' => Region::where('is_active', true)->count(),
                'total_sales' => Sale::count(),
                'books_sold' => (int) SaleItem::sum('quantity'),
            ],
        ];
    }

    protected function regionReport(): Collection
    {
        return Region::query()
            ->withCount('sales')
            ->withSum('sales as income_sum', 'total_amount')
            ->withSum('sales as cost_sum', 'total_cost')
            ->orderByDesc('income_sum')
            ->get()
            ->map(function (Region $region) {
                $income = (float) ($region->income_sum ?? 0);
                $cost = (float) ($region->cost_sum ?? 0);
                $saleIds = Sale::where('region_id', $region->id)->pluck('id');

                return [
                    'name' => $region->name,
                    'sales_count' => (int) $region->sales_count,
                    'quantity' => (int) SaleItem::whereIn('sale_id', $saleIds)->sum('quantity'),
                    'income' => $income,
                    'cost' => $cost,
                    'profit' => $income - $cost,
                ];
            });
    }

    protected function bookReport(): Collection
    {
        return DB::table('sale_items')
            ->join('books', 'books.id', '=', 'sale_items.book_id')
            ->select(
                'books.title',
                'books.author',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.subtotal) as total_income'),
                DB::raw('SUM(sale_items.quantity * sale_items.unit_cost) as total_cost')
            )
            ->groupBy('books.id', 'books.title', 'books.author')
            ->orderByDesc('total_qty')
            ->get()
            ->map(fn ($row) => [
                'title' => $row->title,
                'author' => $row->author,
                'total_qty' => (int) $row->total_qty,
                'total_income' => (float) $row->total_income,
                'total_cost' => (float) $row->total_cost,
                'profit' => (float) $row->total_income - (float) $row->total_cost,
            ]);
    }

    protected function cargoReport(): Collection
    {
        $totals = Sale::query()
            ->select('cargo_status', DB::raw('COUNT(*) as total'))
            ->groupBy('cargo_status')
            ->pluck('total', 'cargo_status');

        $all = Sale::count() ?: 1;

        return collect(Sale::cargoStatuses())->map(function (string $label, string $key) use ($totals, $all) {
            $count = (int) ($totals[$key] ?? 0);

            return [
                'key' => $key,
                'label' => $label,
                'count' => $count,
                'percent' => round(($count / $all) * 100, 1),
                'color' => Sale::cargoStatusColors()[$key] ?? 'gray',
            ];
        })->values();
    }

    protected function expenseReport(): Collection
    {
        return Expense::query()
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'category' => Expense::categories()[$row->category] ?? ($row->category ?: 'Diğer'),
                'total' => (float) $row->total,
            ]);
    }

    public function formatMoney(float|int|string|null $value): string
    {
        return number_format((float) $value, 2, ',', '.').' ₺';
    }
}
