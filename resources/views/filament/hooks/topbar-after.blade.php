@php
    use App\Models\Book;
    use App\Models\Expense;
    use App\Models\Sale;
    use App\Models\SaleItem;
    use Illuminate\Support\Number;

    $todayIncome = (float) Sale::whereDate('sale_date', today())->sum('total_amount');
    $monthIncome = (float) Sale::whereMonth('sale_date', now()->month)
        ->whereYear('sale_date', now()->year)
        ->sum('total_amount');
    $monthExpense = (float) Expense::whereMonth('expense_date', now()->month)
        ->whereYear('expense_date', now()->year)
        ->sum('amount');
    $monthCost = (float) Sale::whereMonth('sale_date', now()->month)
        ->whereYear('sale_date', now()->year)
        ->sum('total_cost');
    $monthProfit = $monthIncome - $monthCost - $monthExpense;
    $todayQty = (int) SaleItem::whereHas('sale', fn ($q) => $q->whereDate('sale_date', today()))->sum('quantity');
    $lowStock = Book::where('is_active', true)->where('stock', '<', 20)->count();
@endphp

<div class="dk-subheader">
    <div class="dk-subheader-inner">
        <div class="dk-subheader-meta">
            <span class="dk-meta-pill">
                <span class="dk-meta-dot"></span>
                Canlı panel
            </span>
            <span class="dk-meta-text hidden sm:inline">Dernek kitap satış ve finans takibi</span>
        </div>

        <div class="dk-stat-row">
            <div class="dk-stat-chip">
                <span class="dk-stat-label">Bugün</span>
                <span class="dk-stat-value text-emerald-600 dark:text-emerald-400">
                    {{ Number::currency($todayIncome, 'TRY', 'tr') }}
                </span>
                <span class="dk-stat-hint">{{ number_format($todayQty, 0, ',', '.') }} adet</span>
            </div>
            <div class="dk-stat-chip">
                <span class="dk-stat-label">Bu ay gelir</span>
                <span class="dk-stat-value">
                    {{ Number::currency($monthIncome, 'TRY', 'tr') }}
                </span>
            </div>
            <div class="dk-stat-chip">
                <span class="dk-stat-label">Bu ay kar</span>
                <span @class([
                    'dk-stat-value',
                    'text-teal-600 dark:text-teal-400' => $monthProfit >= 0,
                    'text-rose-600 dark:text-rose-400' => $monthProfit < 0,
                ])>
                    {{ Number::currency($monthProfit, 'TRY', 'tr') }}
                </span>
            </div>
            <div @class([
                'dk-stat-chip',
                'dk-stat-chip-alert' => $lowStock > 0,
            ])>
                <span class="dk-stat-label">Düşük stok</span>
                <span @class([
                    'dk-stat-value',
                    'text-amber-600 dark:text-amber-400' => $lowStock > 0,
                ])>
                    {{ $lowStock }} kitap
                </span>
            </div>
        </div>
    </div>
</div>
