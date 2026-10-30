<?php

use App\Http\Controllers\ApiWireController;
use App\Http\Controllers\AssessmentController as A;
use App\Http\Controllers\AuthController;
use Filament\Auth\MultiFactor\Http\Middleware\EnsureMultiFactorAuthenticationIsEnabled;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\SetUpPanel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', [A::class, 'home'])->name('home');
Route::get('/saved', [A::class, 'index'])->name('runs.index');
Route::post('/runs', [A::class, 'store'])->middleware('throttle:30,1')->name('runs.store');
Route::get('/runs/{run}', [A::class, 'show'])->name('runs.show');
Route::post('/runs/{run}/answers', [A::class, 'answer'])->middleware('throttle:120,1')->name('answers.store');
Route::get('/runs/{run}/review', [A::class, 'review'])->name('runs.review');
Route::post('/runs/{run}/reports', [A::class, 'report'])->middleware('throttle:20,1')->name('reports.store');
Route::get('/reports/{report}', [A::class, 'result'])->name('reports.show');
Route::get('/reports/{report}/export', [A::class, 'export'])->name('reports.export');
Route::post('/reports/{report}/scenarios', [A::class, 'scenario'])->middleware('throttle:20,1')->name('scenarios.store');
Route::post('/reports/{report}/reprice', [A::class, 'reprice'])->middleware('throttle:20,1')->name('reports.reprice');
Route::post('/locale', [A::class, 'locale'])->name('locale.switch');
Route::post('/resume', [A::class, 'resume'])->middleware('throttle:10,1')->name('resume');
Route::post('/support', [A::class, 'support'])->middleware('throttle:5,1')->name('support.store');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/runs/{run}/link', [AuthController::class, 'link'])->middleware('auth')->name('runs.link');

Route::prefix('api/v1')->middleware('throttle:120,1')->group(function () {
    $wire = ApiWireController::class;
    Route::get('/flows', [$wire, 'flows']);
    Route::post('/runs', [$wire, 'create'])->middleware('throttle:30,1');
    Route::get('/runs/{run}', [$wire, 'show']);
    Route::put('/runs/{run}/answers', [$wire, 'answers']);
    Route::post('/runs/{run}/answers', [A::class, 'answer']);
    Route::post('/runs/{run}/reports', [$wire, 'generate']);
    Route::get('/reports/{report}', [$wire, 'result']);
    Route::post('/reports/{report}/exports', [$wire, 'exports']);
    Route::get('/exports/{export}', [$wire, 'export']);
    Route::get('/exports/{export}/download', [A::class, 'download']);
    Route::get('/operations/{operation}', [$wire, 'poll']);
    Route::get('/estimates/{report}', [$wire, 'estimate']);
    Route::post('/estimates/{report}/revisions', [$wire, 'reprice']);
});
Route::prefix('api/v1/staff')->middleware([SetUpPanel::class.':admin', Authenticate::class, EnsureMultiFactorAuthenticationIsEnabled::class])->group(function () {
    $wire = ApiWireController::class;
    Route::get('/drafts/{flow}', [$wire, 'draft']);
    Route::post('/drafts/{flow}/publications', [$wire, 'publish'])->middleware('throttle:10,1');
});

Route::get('/up', function () {
    try {
        DB::select('SELECT 1');

        return response()->json(['status' => 'up'], 200, ['Cache-Control' => 'no-store']);
    } catch (Throwable $error) {
        report($error);

        return response()->json(['status' => 'down'], 503, ['Cache-Control' => 'no-store']);
    }
})->withoutMiddleware('web')->name('health');
