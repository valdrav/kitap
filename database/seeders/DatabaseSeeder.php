<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Expense;
use App\Models\Region;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@kurtulum.com'],
            [
                'name' => 'Yönetici',
                'password' => Hash::make('Kurtulum2026!'),
            ]
        );

        // Eski varsayılan hesabı temizle
        User::query()->where('email', 'admin@dernek.test')->delete();

        $regions = collect([
            ['name' => 'A Bölgesi', 'code' => 'BOLGE-A', 'description' => 'Merkez ve çevresi'],
            ['name' => 'B Bölgesi', 'code' => 'BOLGE-B', 'description' => 'Kuzey illeri'],
            ['name' => 'C Bölgesi', 'code' => 'BOLGE-C', 'description' => 'Güney illeri'],
            ['name' => 'D Bölgesi', 'code' => 'BOLGE-D', 'description' => 'Doğu illeri'],
        ])->map(fn (array $data) => Region::query()->updateOrCreate(
            ['code' => $data['code']],
            [...$data, 'is_active' => true]
        ));

        SaleItem::query()->delete();
        Sale::query()->delete();

        $books = collect([
            ['title' => 'Ali Veli', 'author' => 'Ahmet Yılmaz', 'sale_price' => 150, 'cost_price' => 90, 'stock' => 200],
            ['title' => 'Mehmet', 'author' => 'Ayşe Demir', 'sale_price' => 120, 'cost_price' => 70, 'stock' => 180],
            ['title' => 'Yolculuk', 'author' => 'Can Özkan', 'sale_price' => 180, 'cost_price' => 110, 'stock' => 150],
            ['title' => 'Umut', 'author' => 'Elif Kara', 'sale_price' => 100, 'cost_price' => 55, 'stock' => 220],
            ['title' => 'Birlik', 'author' => 'Dernek Yayınları', 'sale_price' => 200, 'cost_price' => 130, 'stock' => 100],
        ])->map(fn (array $data) => Book::query()->updateOrCreate(
            ['title' => $data['title']],
            [...$data, 'is_active' => true, 'description' => $data['title'].' kitabı']
        ));

        $sampleSales = [
            [
                'region' => 'BOLGE-A',
                'date' => now()->subDays(12)->toDateString(),
                'cargo_status' => Sale::STATUS_TESLIM_EDILDI,
                'cargo_company' => 'yurtici',
                'tracking_number' => 'YK7845123390',
                'items' => [
                    ['title' => 'Ali Veli', 'qty' => 20],
                    ['title' => 'Mehmet', 'qty' => 10],
                ],
            ],
            [
                'region' => 'BOLGE-B',
                'date' => now()->subDays(8)->toDateString(),
                'cargo_status' => Sale::STATUS_YOLDA,
                'cargo_company' => 'aras',
                'tracking_number' => 'AR9988776655',
                'items' => [
                    ['title' => 'Yolculuk', 'qty' => 15],
                    ['title' => 'Umut', 'qty' => 25],
                ],
            ],
            [
                'region' => 'BOLGE-C',
                'date' => now()->subDays(3)->toDateString(),
                'cargo_status' => Sale::STATUS_TESLIM_EDILEMEDI,
                'cargo_company' => 'mng',
                'tracking_number' => 'MNG4455667788',
                'status_note' => 'Adreste kimse bulunamadı',
                'items' => [
                    ['title' => 'Ali Veli', 'qty' => 12],
                    ['title' => 'Birlik', 'qty' => 8],
                    ['title' => 'Mehmet', 'qty' => 5],
                ],
            ],
            [
                'region' => 'BOLGE-A',
                'date' => now()->subDays(1)->toDateString(),
                'cargo_status' => Sale::STATUS_KARGOYA_VERILDI,
                'cargo_company' => 'ptt',
                'tracking_number' => 'PTT1122334455',
                'items' => [
                    ['title' => 'Umut', 'qty' => 30],
                    ['title' => 'Birlik', 'qty' => 10],
                ],
            ],
        ];

        foreach ($sampleSales as $sample) {
            $region = $regions->firstWhere('code', $sample['region']);
            $sale = Sale::query()->create([
                'region_id' => $region->id,
                'user_id' => $admin->id,
                'sale_date' => $sample['date'],
                'total_amount' => 0,
                'total_cost' => 0,
                'notes' => $region->name.' satış kaydı',
                'cargo_status' => $sample['cargo_status'],
                'cargo_company' => $sample['cargo_company'] ?? null,
                'tracking_number' => $sample['tracking_number'] ?? null,
                'status_note' => $sample['status_note'] ?? null,
                'status_updated_at' => now()->subDays(rand(0, 5)),
            ]);

            foreach ($sample['items'] as $item) {
                $book = $books->firstWhere('title', $item['title']);
                $qty = $item['qty'];

                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'book_id' => $book->id,
                    'quantity' => $qty,
                    'unit_price' => $book->sale_price,
                    'unit_cost' => $book->cost_price,
                    'subtotal' => $qty * $book->sale_price,
                ]);

                $book->decrement('stock', $qty);
            }

            $sale->recalculateTotals();
        }

        Expense::query()->delete();

        $expenses = [
            ['title' => 'Baskı gideri', 'category' => 'basim', 'amount' => 4500, 'expense_date' => now()->subDays(20)],
            ['title' => 'Kargo ödemesi', 'category' => 'kargo', 'amount' => 1200, 'expense_date' => now()->subDays(10)],
            ['title' => 'Ofis malzemeleri', 'category' => 'ofis', 'amount' => 850, 'expense_date' => now()->subDays(5)],
            ['title' => 'Tanıtım afişleri', 'category' => 'reklam', 'amount' => 1500, 'expense_date' => now()->subDays(2)],
        ];

        foreach ($expenses as $expense) {
            Expense::query()->create($expense);
        }
    }
}
