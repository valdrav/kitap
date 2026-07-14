<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Book;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected static ?string $title = 'Yeni Satış Kaydı';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status_updated_at'] = $data['status_updated_at'] ?? now();
        $data['cargo_status'] = $data['cargo_status'] ?? 'hazirlaniyor';
        $items = $this->form->getState()['items'] ?? [];

        $data['total_amount'] = collect($items)->sum(fn ($item) => (float) ($item['subtotal'] ?? ((float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0))));
        $data['total_cost'] = collect($items)->sum(fn ($item) => (float) ($item['quantity'] ?? 0) * (float) ($item['unit_cost'] ?? 0));

        return $data;
    }

    protected function afterCreate(): void
    {
        foreach ($this->record->items as $item) {
            Book::where('id', $item->book_id)->decrement('stock', $item->quantity);
        }

        $this->record->recalculateTotals();
    }
}
