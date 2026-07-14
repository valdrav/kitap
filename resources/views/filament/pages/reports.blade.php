<x-filament-panels::page>
    <div class="dk-reports space-y-6">
        {{-- Meta strip --}}
        <div class="dk-report-meta">
            <div class="dk-report-meta-item">
                <span class="dk-report-meta-label">Toplam Satış</span>
                <span class="dk-report-meta-value">{{ number_format($meta['total_sales'], 0, ',', '.') }}</span>
            </div>
            <div class="dk-report-meta-item">
                <span class="dk-report-meta-label">Satılan Kitap</span>
                <span class="dk-report-meta-value">{{ number_format($meta['books_sold'], 0, ',', '.') }}</span>
            </div>
            <div class="dk-report-meta-item">
                <span class="dk-report-meta-label">Aktif Kitap</span>
                <span class="dk-report-meta-value">{{ $meta['active_books'] }}</span>
            </div>
            <div class="dk-report-meta-item">
                <span class="dk-report-meta-label">Aktif Bölge</span>
                <span class="dk-report-meta-value">{{ $meta['active_regions'] }}</span>
            </div>
        </div>

        {{-- Region report --}}
        <x-filament::section
            heading="Bölge Bazlı Analiz"
            description="Her bölgedeki satış, adet, gelir ve kar özeti"
            icon="heroicon-o-map"
            collapsible
        >
            <div class="dk-table-wrap">
                <table class="dk-table">
                    <thead>
                        <tr>
                            <th>Bölge</th>
                            <th class="text-right">Satış</th>
                            <th class="text-right">Adet</th>
                            <th class="text-right">Gelir</th>
                            <th class="text-right">Maliyet</th>
                            <th class="text-right">Kar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byRegion as $row)
                            <tr>
                                <td>
                                    <span class="dk-cell-title">{{ $row['name'] }}</span>
                                </td>
                                <td class="text-right">{{ $row['sales_count'] }}</td>
                                <td class="text-right">{{ number_format($row['quantity'], 0, ',', '.') }}</td>
                                <td class="text-right dk-money-in">{{ $this->formatMoney($row['income']) }}</td>
                                <td class="text-right">{{ $this->formatMoney($row['cost']) }}</td>
                                <td @class([
                                    'text-right font-semibold',
                                    'dk-money-profit' => $row['profit'] >= 0,
                                    'dk-money-loss' => $row['profit'] < 0,
                                ])>{{ $this->formatMoney($row['profit']) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="dk-empty">Henüz bölge satışı yok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <div class="grid gap-6 xl:grid-cols-2">
            {{-- Book report --}}
            <x-filament::section
                heading="Kitap Bazlı Satış"
                description="Hangi kitaptan kaç adet satıldığı"
                icon="heroicon-o-book-open"
                collapsible
            >
                <div class="dk-table-wrap">
                    <table class="dk-table">
                        <thead>
                            <tr>
                                <th>Kitap</th>
                                <th class="text-right">Adet</th>
                                <th class="text-right">Gelir</th>
                                <th class="text-right">Kar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($byBook as $row)
                                <tr>
                                    <td>
                                        <span class="dk-cell-title">{{ $row['title'] }}</span>
                                        @if ($row['author'])
                                            <span class="dk-cell-sub">{{ $row['author'] }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format($row['total_qty'], 0, ',', '.') }}</td>
                                    <td class="text-right dk-money-in">{{ $this->formatMoney($row['total_income']) }}</td>
                                    <td @class([
                                        'text-right font-semibold',
                                        'dk-money-profit' => $row['profit'] >= 0,
                                        'dk-money-loss' => $row['profit'] < 0,
                                    ])>{{ $this->formatMoney($row['profit']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="dk-empty">Henüz kitap satışı yok.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>

            {{-- Cargo + expenses --}}
            <div class="space-y-6">
                <x-filament::section
                    heading="Kargo Durum Dağılımı"
                    description="Satışların kargo durumlarına göre dağılımı"
                    icon="heroicon-o-truck"
                    collapsible
                >
                    <div class="space-y-3">
                        @foreach ($byCargo as $row)
                            <div class="dk-cargo-row">
                                <div class="dk-cargo-row-top">
                                    <span @class([
                                        'dk-cargo-badge',
                                        'dk-badge-' . $row['color'],
                                    ])>{{ $row['label'] }}</span>
                                    <span class="dk-cargo-count">{{ $row['count'] }} satış · %{{ $row['percent'] }}</span>
                                </div>
                                <div class="dk-progress">
                                    <div
                                        class="dk-progress-bar dk-bar-{{ $row['color'] }}"
                                        style="width: {{ min($row['percent'], 100) }}%"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>

                <x-filament::section
                    heading="Gider Kategorileri"
                    description="Diğer giderlerin kategori kırılımı"
                    icon="heroicon-o-banknotes"
                    collapsible
                >
                    <div class="space-y-2">
                        @forelse ($byExpenseCategory as $row)
                            <div class="dk-expense-row">
                                <span class="dk-cell-title">{{ $row['category'] }}</span>
                                <span class="dk-money-loss font-semibold">{{ $this->formatMoney($row['total']) }}</span>
                            </div>
                        @empty
                            <p class="dk-empty">Henüz gider kaydı yok.</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>
        </div>
    </div>
</x-filament-panels::page>
