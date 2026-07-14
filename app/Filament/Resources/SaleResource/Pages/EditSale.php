<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use App\Models\Book;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected static ?string $title = 'Satış Düzenle';

    protected array $originalItems = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('Görüntüle'),
            Actions\DeleteAction::make()
                ->label('Sil')
                ->before(function (): void {
                    foreach ($this->record->items as $item) {
                        Book::where('id', $item->book_id)->increment('stock', $item->quantity);
                    }
                }),
        ];
    }

    protected function beforeSave(): void
    {
        $this->originalItems = $this->record->items()
            ->get(['book_id', 'quantity'])
            ->map(fn ($item) => ['book_id' => $item->book_id, 'quantity' => $item->quantity])
            ->all();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $items = $this->form->getState()['items'] ?? [];

        $data['total_amount'] = collect($items)->sum(fn ($item) => (float) ($item['subtotal'] ?? ((float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0))));
        $data['total_cost'] = collect($items)->sum(fn ($item) => (float) ($item['quantity'] ?? 0) * (float) ($item['unit_cost'] ?? 0));

        return $data;
    }

    protected function afterSave(): void
    {
        foreach ($this->originalItems as $item) {
            Book::where('id', $item['book_id'])->increment('stock', $item['quantity']);
        }

        $this->record->refresh()->load('items');

        foreach ($this->record->items as $item) {
            Book::where('id', $item->book_id)->decrement('stock', $item->quantity);
        }

        $this->record->recalculateTotals();
    }
}
