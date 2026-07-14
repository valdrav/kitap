<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('Dernek Kitap')
            ->brandLogo(asset('images/brand-logo.svg'))
            ->darkModeBrandLogo(asset('images/brand-logo-dark.svg'))
            ->brandLogoHeight('2.35rem')
            ->favicon(asset('images/favicon.svg'))
            ->colors([
                'primary' => Color::hex('#0F766E'),
                'gray' => Color::Slate,
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->font('Figtree')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::Full)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldKeyBindingSuffix()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Satış Yönetimi')
                    ->icon('heroicon-o-shopping-bag')
                    ->collapsible(),
                NavigationGroup::make('Ürünler')
                    ->icon('heroicon-o-book-open')
                    ->collapsible(),
                NavigationGroup::make('Finans')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible(),
                NavigationGroup::make('Tanımlar')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Ayarlar')
                    ->url(fn (): string => \App\Filament\Pages\Settings::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
                MenuItem::make()
                    ->label('Yeni Satış')
                    ->url(fn (): string => \App\Filament\Resources\SaleResource::getUrl('create'))
                    ->icon('heroicon-o-plus-circle'),
                MenuItem::make()
                    ->label('Raporlar')
                    ->url(fn (): string => \App\Filament\Pages\Reports::getUrl())
                    ->icon('heroicon-o-document-chart-bar'),
                'logout' => MenuItem::make()
                    ->label('Çıkış Yap')
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
            ])
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): string => view('filament.hooks.login-brand')->render(),
            )
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => view('filament.hooks.custom-styles')->render(),
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => auth()->check()
                    ? view('filament.hooks.topbar-brand')->render()
                    : '',
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn (): string => auth()->check()
                    ? view('filament.hooks.topbar-actions')->render()
                    : '',
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_AFTER,
                fn (): string => auth()->check()
                    ? view('filament.hooks.topbar-after')->render()
                    : '',
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => view('filament.hooks.sidebar-footer')->render(),
            );
    }
}
