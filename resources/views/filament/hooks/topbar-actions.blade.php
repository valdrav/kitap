@php
    use App\Filament\Resources\BookResource;
    use App\Filament\Resources\ExpenseResource;
    use App\Filament\Resources\SaleResource;
    use App\Filament\Pages\Reports;
@endphp

<div class="dk-topbar-actions hidden items-center gap-1.5 lg:flex">
    <a href="{{ SaleResource::getUrl('create') }}" class="dk-nav-action dk-nav-action-primary" title="Yeni satış kaydı">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
        </svg>
        <span>Satış</span>
    </a>
    <a href="{{ BookResource::getUrl('create') }}" class="dk-nav-action" title="Yeni kitap ekle">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
            <path d="M10.75 16.82A7.462 7.462 0 0 1 15 15.5c.71 0 1.396.098 2.046.282A.75.75 0 0 0 18 15.06v-11a.75.75 0 0 0-.546-.721A9.006 9.006 0 0 0 15 3a9.006 9.006 0 0 0-2.454.333A.75.75 0 0 0 12 4.06v11a.75.75 0 0 0 .75.75Z" />
            <path d="M3.954 4.061A.75.75 0 0 0 3 4.78v10.28A7.466 7.466 0 0 1 7.5 13.5c.85 0 1.67.14 2.436.4A.75.75 0 0 0 11 13.18V2.87a.75.75 0 0 0-.954-.721A9.006 9.006 0 0 0 7.5 2a9.006 9.006 0 0 0-3.546.79Z" />
        </svg>
        <span>Kitap</span>
    </a>
    <a href="{{ ExpenseResource::getUrl('create') }}" class="dk-nav-action" title="Gider ekle">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
            <path fill-rule="evenodd" d="M1 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4Zm12 4a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM2 15a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H3a1 1 0 0 1-1-1Z" clip-rule="evenodd" />
        </svg>
        <span>Gider</span>
    </a>
    <a href="{{ Reports::getUrl() }}" class="dk-nav-action" title="Detaylı raporlar">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
            <path d="M15.5 2A1.5 1.5 0 0 0 14 3.5v13a1.5 1.5 0 0 0 1.5 1.5h1a1.5 1.5 0 0 0 1.5-1.5v-13A1.5 1.5 0 0 0 16.5 2h-1ZM9.5 6A1.5 1.5 0 0 0 8 7.5v9A1.5 1.5 0 0 0 9.5 18h1a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 10.5 6h-1ZM3.5 10A1.5 1.5 0 0 0 2 11.5v5A1.5 1.5 0 0 0 3.5 18h1A1.5 1.5 0 0 0 6 16.5v-5A1.5 1.5 0 0 0 4.5 10h-1Z" />
        </svg>
        <span>Rapor</span>
    </a>
</div>
