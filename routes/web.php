<?php

use Illuminate\Support\Facades\Route;

/*
| Filament panel path '' olduğu için / paneli kapsar.
| Burada / → /login yönlendirmesi YAPILMAZ; aksi halde giriş sonrası
| login → / → login döngüsü (ERR_TOO_MANY_REDIRECTS) oluşur.
| Misafirler Filament auth middleware ile /login'e alınır.
*/

Route::view('/welcome', 'welcome');
