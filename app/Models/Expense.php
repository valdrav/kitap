<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'title',
        'category',
        'amount',
        'expense_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public static function categories(): array
    {
        return [
            'basim' => 'Basım / Baskı',
            'kargo' => 'Kargo / Nakliye',
            'personel' => 'Personel',
            'ofis' => 'Ofis / Kırtasiye',
            'reklam' => 'Tanıtım / Reklam',
            'diger' => 'Diğer',
        ];
    }
}
