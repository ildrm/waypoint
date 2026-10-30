<?php

namespace App\Filament\Pages;

use App\Domain\AdminWorkspace;
use Filament\Pages\Page;

class Workspace extends Page
{
    protected string $view = 'filament.pages.workspace';

    protected static ?string $title = 'Waypoint workspace';

    protected static ?string $slug = 'workspace';

    public static function canAccess(): bool
    {
        return auth()->user()?->enabled ?? false;
    }

    protected function getViewData(): array
    {
        return app(AdminWorkspace::class)->data(request());
    }
}
