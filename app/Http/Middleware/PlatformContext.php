<?php

namespace App\Http\Middleware;

use App\Domain\SourceCopy;
use App\Domain\TranslationManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

final class PlatformContext
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', 'en');
        if (! array_key_exists($locale, config('platform.locales'))) {
            $locale = 'en';
        }app()->setLocale($locale);
        $preview = app()->environment(['local', 'testing']) && config('platform.source_preview');
        View::share(['locale' => $locale, 'locales' => config('platform.locales'), 'preview' => $preview]);
        if (auth()->check() && ! auth()->user()->enabled && $request->path() !== 'logout') {
            abort(403);
        }
        $unavailable = false;
        if (! $preview && ! $request->is('admin*') && ! $request->is('livewire*') && ! $request->is('login') && ! $request->is('logout') && ! $request->is('locale') && ! $request->is('support') && ! $request->is('resume') && ! $request->is('api/*') && ! ($request->is('reports/*/export') && $request->query('format', 'json') === 'json') && Schema::hasTable('translations')) {
            $keys = SourceCopy::keys();
            $coverage = app(TranslationManager::class)->coverage('ui', $keys);
            if (count($coverage[$locale]['missing'] ?? [])) {
                $unavailable = true;
            }
        }$response = $unavailable ? response()->view('locale-unavailable', [], 409) : $next($request);
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Cache-Control', 'private,no-store');
        if (! $request->is('admin*') && ! $request->is('livewire*')) {
            $response->headers->set('Content-Security-Policy', "default-src 'self';script-src 'self';style-src 'self';img-src 'self' data:;font-src 'self';frame-ancestors 'none';base-uri 'self';form-action 'self'");
        }

        return $response;
    }
}
