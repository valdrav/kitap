<?php

use Illuminate\Support\Facades\Route;

// Kök: girişe gönder (Filament path '')
Route::redirect('/', '/login');

// Eski Laravel karşılama sayfası bir şekilde açılırsa bile login'e al
Route::view('/welcome', 'welcome');
