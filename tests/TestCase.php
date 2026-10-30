<?php

namespace Tests;

use App\Models\AssessmentRun;
use App\Models\Flow;
use App\Models\FlowRelease;
use App\Models\PriceBook;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    protected function bank(string $slug): array
    {
        return json_decode(file_get_contents(base_path('docs/03-workflows/'.$slug.'/question-bank.yaml')), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function flow(string $slug = 'software', ?array $bank = null, array $extra = []): Flow
    {
        $bank ??= $this->bank($slug);
        $flow = Flow::create(['slug' => $slug.'-'.Str::lower(Str::random(6)), 'name' => ucfirst($slug), 'description' => 'Synthetic testing context', 'bank' => $bank, 'rules' => [], 'question_count' => count($bank['questions']), 'status' => 'preview']);
        $manifest = ['bank' => $bank, 'rules' => [], 'settings' => [], 'translations' => [], 'ui_translations' => [], 'source_preview' => true, 'catalog' => [], 'method_version' => '1.0'];
        $manifest = array_replace($manifest, $extra);
        $release = FlowRelease::create(['flow_id' => $flow->id, 'version' => 1, 'hash' => hash('sha256', json_encode($manifest)), 'manifest' => $manifest, 'status' => 'preview', 'published_at' => now()]);
        $flow->update(['active_release_id' => $release->id]);

        return $flow;
    }

    protected function runFor(Flow $f, array $answers = []): AssessmentRun
    {
        $this->withSession(['assessment_principal' => 'synthetic-owner', 'locale' => 'en']);

        return AssessmentRun::create(['flow_id' => $f->id, 'release_id' => $f->active_release_id, 'principal_hash' => hash('sha256', 'synthetic-owner'), 'locale' => 'en', 'answers' => $answers, 'last_activity_at' => now(), 'expires_at' => now()->addDays(7)]);
    }

    protected function book(array $packages, string $rate = '60.00', int $version = 1): PriceBook
    {
        return PriceBook::create(['name' => 'Synthetic test only', 'version' => $version, 'currency' => 'USD', 'exponent' => 2, 'market' => 'test', 'status' => 'approved', 'synthetic' => true, 'rates' => ['DEV' => $rate], 'internal_rates' => ['DEV' => '20.00'], 'work_packages' => $packages, 'recurring' => [['interval' => 'month', 'amount' => '5.00']], 'contingency' => '0', 'discount' => '0', 'validity_days' => 30, 'effective_from' => now()->subDay()]);
    }

    protected function staff(array $capabilities = ['security_admin']): User
    {
        return User::create(['name' => 'Test staff', 'email' => Str::random(8).'@example.invalid', 'password' => 'synthetic-test-password', 'capabilities' => $capabilities, 'enabled' => true, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    }
}
