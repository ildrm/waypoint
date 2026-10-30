<?php

use App\Domain\Audit;
use App\Domain\BackupService;
use App\Domain\DemoFixtures;
use App\Domain\PrivacyService;
use App\Domain\PublicationService;
use App\Domain\SourceCopy;
use App\Domain\TranslationManager;
use App\Models\AssessmentRun;
use App\Models\ExportJob;
use App\Models\Flow;
use App\Models\Idempotency;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;

Artisan::command('platform:bootstrap-admin {--email=} {--name=}', function () {
    if (! app()->environment('local') && ! $this->confirm('Create an explicitly authorized staff account?', false)) {
        return 1;
    }$email = $this->option('email') ?: $this->ask('Staff email');
    $name = $this->option('name') ?: $this->ask('Staff name');
    if (! filter_var($email, FILTER_VALIDATE_EMAIL) || User::where('email', $email)->exists()) {
        $this->error('Use a new valid email.');

        return 1;
    }$password = $this->secret('Password (at least 14 characters)');
    if (! is_string($password) || strlen($password) < 14) {
        $this->error('Password is too short.');

        return 1;
    }$confirm = $this->secret('Repeat password');
    if (! hash_equals($password, $confirm)) {
        $this->error('Passwords differ.');

        return 1;
    }User::create(['email' => $email, 'name' => $name, 'password' => $password, 'capabilities' => ['security_admin'], 'enabled' => true]);
    $this->info('Staff created. Open /admin; authenticator setup is required.');
});
Artisan::command('platform:preview {slug?}', function () {
    if (! app()->environment(['local', 'testing'])) {
        $this->error('Preview is local/test only.');

        return 1;
    }foreach (Flow::when($this->argument('slug'), fn ($q) => $q->where('slug', $this->argument('slug')))->get() as $flow) {
        app(PublicationService::class)->publish($flow, null, true);
        $this->info($flow->slug.': source preview released.');
    }
});
Artisan::command('platform:translations', function () {
    app(TranslationManager::class)->sync('ui', SourceCopy::keys());
    foreach (Flow::all() as $f) {
        $keys = app(TranslationManager::class)->sourceKeys($f->bank);
        app(TranslationManager::class)->sync($f->id, $keys);
        $coverage = app(TranslationManager::class)->coverage($f->id, $keys);
        foreach ($coverage as $locale => $row) {
            $this->line($f->slug.' '.$locale.': '.$row['approved'].'/'.$row['required'].' reviewed');
        }
    }
});
Artisan::command('platform:prune {--apply}', function () {
    if ($this->option('apply')) {
        Idempotency::where('expires_at', '<', now())->delete();
    }
    if (! config('platform.retention_approved')) {
        $this->error('Retention policy has not been approved; no deletion performed.');

        return 1;
    }$runs = AssessmentRun::whereNull('user_id')->where('expires_at', '<', now())->where('status', '!=', 'deleted')->get();
    $this->info($runs->count().' expired anonymous runs.');
    if (! $this->option('apply')) {
        return;
    }foreach ($runs as $run) {
        app(PrivacyService::class)->erase($run, null);
    }Idempotency::where('expires_at', '<', now())->delete();
});
Schedule::command('platform:prune --apply')->daily()->withoutOverlapping();

Artisan::command('platform:backup {--verify-restore}', function () {
    $service = app(BackupService::class);
    $backup = $service->create();
    $this->info(json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    if ($this->option('verify-restore')) {
        $result = $service->restoreToNewFile($backup['path'], 'exercise-'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(3)).'.sqlite');
        $this->info(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
});
Artisan::command('platform:reset-password {email}', function () {
    $user = User::where('email', $this->argument('email'))->firstOrFail();
    $password = $this->secret('New password (at least14 characters)');
    $confirm = $this->secret('Repeat password');
    if (strlen($password ?? '') < 14 || ! hash_equals($password, $confirm ?? '')) {
        $this->error('Password mismatch or too short.');

        return 1;
    }$user->update(['password' => $password, 'remember_token' => Str::random(60)]);
    Audit::record('staff.password_reset', (string) $user->id, ['status' => 'operator_reset']);
    $this->info('Password reset. Existing authenticated sessions fail their next password-hash check.');
});

Artisan::command('platform:demo', function () {
    $result = app(DemoFixtures::class)->load();
    $this->info(json_encode($result));
    $this->warn('Synthetic local/test pricing and catalog only; not production approval. Rebuild source previews to include catalog alternatives.');
});
Artisan::command('platform:health', function () {
    DB::select('SELECT 1');
    $this->info(json_encode(['database' => 'reachable', 'queue_pending' => DB::table('jobs')->count(), 'queue_failed' => DB::table('failed_jobs')->count(), 'exports_failed' => ExportJob::where('status', 'failed')->count(), 'source_preview' => (bool) config('platform.source_preview'), 'retention_policy_approved' => (bool) config('platform.retention_approved')], JSON_PRETTY_PRINT));
});
