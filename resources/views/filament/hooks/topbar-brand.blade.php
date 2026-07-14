@php
    $user = filament()->auth()->user();
    $hour = (int) now()->format('G');
    $greeting = match (true) {
        $hour < 12 => 'Günaydın',
        $hour < 18 => 'İyi günler',
        default => 'İyi akşamlar',
    };
@endphp

<div class="dk-topbar-brand hidden min-w-0 items-center gap-3 xl:flex">
    <div class="dk-topbar-brand-mark">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
            <path d="M11.25 4.533A9.707 9.707 0 0 0 6 3a9.735 9.735 0 0 0-3.25.555.75.75 0 0 0-.5.707v14.25a.75.75 0 0 0 1 .707A8.237 8.237 0 0 1 6 18.75c1.995 0 3.823.707 5.25 1.886V4.533ZM12.75 20.636A8.214 8.214 0 0 1 18 18.75c.966 0 1.89.166 2.75.47a.75.75 0 0 0 1-.708V4.262a.75.75 0 0 0-.5-.707A9.735 9.735 0 0 0 18 3a9.707 9.707 0 0 0-5.25 1.533v16.103Z" />
        </svg>
    </div>
    <div class="min-w-0 leading-tight">
        <p class="truncate text-sm font-semibold text-gray-950 dark:text-white">
            {{ $greeting }}, {{ $user?->name ?? 'Yönetici' }}
        </p>
        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
            {{ now()->locale('tr')->translatedFormat('d F Y, l') }}
        </p>
    </div>
    <div class="dk-topbar-divider hidden 2xl:block"></div>
</div>
