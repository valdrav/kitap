<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    public const STATUS_HAZIRLANIYOR = 'hazirlaniyor';

    public const STATUS_KARGOYA_VERILDI = 'kargoya_verildi';

    public const STATUS_YOLDA = 'yolda';

    public const STATUS_TESLIM_EDILDI = 'teslim_edildi';

    public const STATUS_TESLIM_EDILEMEDI = 'teslim_edilemedi';

    public const STATUS_IADE_EDILDI = 'iade_edildi';

    public const STATUS_IPTAL = 'iptal';

    protected $fillable = [
        'region_id',
        'user_id',
        'sale_date',
        'total_amount',
        'total_cost',
        'notes',
        'cargo_status',
        'cargo_company',
        'tracking_number',
        'status_updated_at',
        'status_note',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'total_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'status_updated_at' => 'datetime',
        ];
    }

    public static function cargoStatuses(): array
    {
        return [
            self::STATUS_HAZIRLANIYOR => 'Hazırlanıyor',
            self::STATUS_KARGOYA_VERILDI => 'Kargoya Verildi',
            self::STATUS_YOLDA => 'Yolda / Dağıtımda',
            self::STATUS_TESLIM_EDILDI => 'Teslim Edildi',
            self::STATUS_TESLIM_EDILEMEDI => 'Teslim Edilemedi',
            self::STATUS_IADE_EDILDI => 'İade Edildi',
            self::STATUS_IPTAL => 'İptal',
        ];
    }

    public static function cargoStatusColors(): array
    {
        return [
            self::STATUS_HAZIRLANIYOR => 'gray',
            self::STATUS_KARGOYA_VERILDI => 'info',
            self::STATUS_YOLDA => 'warning',
            self::STATUS_TESLIM_EDILDI => 'success',
            self::STATUS_TESLIM_EDILEMEDI => 'danger',
            self::STATUS_IADE_EDILDI => 'danger',
            self::STATUS_IPTAL => 'gray',
        ];
    }

    public static function cargoCompanies(): array
    {
        return [
            'yurtici' => 'Yurtiçi Kargo',
            'aras' => 'Aras Kargo',
            'mng' => 'MNG Kargo',
            'ptt' => 'PTT Kargo',
            'surat' => 'Sürat Kargo',
            'trendyol' => 'Trendyol Express',
            'hepsijet' => 'HepsiJet',
            'diger' => 'Diğer',
        ];
    }

    public function getCargoStatusLabelAttribute(): string
    {
        return self::cargoStatuses()[$this->cargo_status] ?? $this->cargo_status;
    }

    public function getCargoStatusColorAttribute(): string
    {
        return self::cargoStatusColors()[$this->cargo_status] ?? 'gray';
    }

    public function getCargoCompanyLabelAttribute(): ?string
    {
        if (! $this->cargo_company) {
            return null;
        }

        return self::cargoCompanies()[$this->cargo_company] ?? $this->cargo_company;
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->total_cost;
    }

    public function updateCargoStatus(string $status, ?string $note = null): void
    {
        $this->update([
            'cargo_status' => $status,
            'status_note' => $note ?? $this->status_note,
            'status_updated_at' => now(),
        ]);
    }

    public function recalculateTotals(): void
    {
        $this->loadMissing('items');

        $this->update([
            'total_amount' => $this->items->sum('subtotal'),
            'total_cost' => $this->items->sum(fn (SaleItem $item) => $item->quantity * $item->unit_cost),
        ]);
    }
}
