<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Workspace;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\PlatformContext;
use App\Support\LocalAvatarProvider;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Http\Middleware\EnsureMultiFactorAuthenticationIsEnabled;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel->default()->id('admin')->path('admin')->brandName('Waypoint · Administration')->defaultAvatarProvider(LocalAvatarProvider::class)->login()->profile()->multiFactorAuthentication([AppAuthentication::make()->recoverable()->codeWindow(1)], isRequired: true)->pages([Workspace::class])->middleware([EncryptCookies::class, AddQueuedCookiesToResponse::class, StartSession::class, AuthenticateSession::class, ShareErrorsFromSession::class, ValidateCsrfToken::class, SubstituteBindings::class, DisableBladeIconComponents::class, DispatchServingFilamentEvent::class, PlatformContext::class])->authMiddleware([Authenticate::class])->authenticatedRoutes(function () {
            Route::post('/manage', [AdminController::class, 'manage'])->name('manage')->middleware(['throttle:60,1', EnsureMultiFactorAuthenticationIsEnabled::class]);
        });
    }
}
