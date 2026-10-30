<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->json('capabilities');
            $t->boolean('enabled')->default(true);
            $t->text('app_authentication_secret')->nullable();
            $t->text('app_authentication_recovery_codes')->nullable();
            $t->rememberToken();
            $t->timestamps();
        });
        Schema::create('password_reset_tokens', function (Blueprint $t) {
            $t->string('email')->primary();
            $t->string('token');
            $t->timestamp('created_at')->nullable();
        });
        Schema::create('flows', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('slug')->unique();
            $t->string('name');
            $t->text('description');
            $t->json('bank');
            $t->json('rules');
            $t->unsignedInteger('question_count');
            $t->unsignedInteger('revision')->default(1);
            $t->string('status')->default('draft');
            $t->uuid('active_release_id')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users');
            $t->foreignId('reviewed_by')->nullable()->constrained('users');
            $t->string('reviewed_hash', 64)->nullable();
            $t->timestamps();
        });
        Schema::create('flow_releases', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('flow_id')->constrained()->restrictOnDelete();
            $t->unsignedInteger('version');
            $t->string('hash', 64);
            $t->json('manifest');
            $t->string('status');
            $t->foreignId('published_by')->nullable()->constrained('users');
            $t->timestamp('published_at')->nullable();
            $t->timestamps();
            $t->unique(['flow_id', 'version']);
        });
        Schema::create('assessment_runs', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('flow_id')->constrained()->restrictOnDelete();
            $t->foreignUuid('release_id')->references('id')->on('flow_releases')->restrictOnDelete();
            $t->foreignId('user_id')->nullable()->constrained('users');
            $t->string('principal_hash', 64)->index();
            $t->string('resume_token_hash', 64)->nullable()->unique();
            $t->string('locale', 20);
            $t->unsignedInteger('revision')->default(1);
            $t->string('status')->default('active');
            $t->json('answers');
            $t->uuid('baseline_report_id')->nullable();
            $t->timestamp('last_activity_at');
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });
        Schema::create('answer_revisions', function (Blueprint $t) {
            $t->id();
            $t->foreignUuid('run_id')->references('id')->on('assessment_runs')->cascadeOnDelete();
            $t->unsignedInteger('revision');
            $t->string('question_id');
            $t->string('state');
            $t->json('value')->nullable();
            $t->json('invalidated');
            $t->timestamps();
            $t->unique(['run_id', 'revision']);
        });
        Schema::create('price_books', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('name');
            $t->unsignedInteger('version')->default(1);
            $t->string('currency', 3);
            $t->unsignedTinyInteger('exponent')->default(2);
            $t->string('market');
            $t->string('status')->default('draft');
            $t->boolean('synthetic')->default(false);
            $t->json('rates');
            $t->json('internal_rates')->nullable();
            $t->json('work_packages');
            $t->json('recurring')->nullable();
            $t->string('contingency', 30)->default('0');
            $t->string('discount', 30)->default('0');
            $t->unsignedInteger('validity_days')->default(30);
            $t->timestamp('effective_from');
            $t->timestamp('effective_until')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users');
            $t->foreignId('approved_by')->nullable()->constrained('users');
            $t->timestamps();
            $t->index(['currency', 'market', 'status', 'effective_from']);
        });
        Schema::create('catalog_items', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('stable_id')->unique();
            $t->string('name');
            $t->string('domain');
            $t->text('description');
            $t->json('eligibility');
            $t->json('deliverables');
            $t->json('preferences');
            $t->json('dimensions')->nullable();
            $t->string('status')->default('draft');
            $t->boolean('synthetic')->default(false);
            $t->unsignedInteger('version')->default(1);
            $t->timestamps();
        });
        Schema::create('parameter_definitions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('name');
            $t->string('type');
            $t->string('unit');
            $t->json('value')->nullable();
            $t->json('constraints');
            $t->boolean('allow_override')->default(false);
            $t->boolean('nullable')->default(false);
            $t->unsignedInteger('revision')->default(1);
            $t->timestamps();
        });
        Schema::create('parameter_overrides', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('flow_id')->constrained()->cascadeOnDelete();
            $t->string('parameter_id');
            $t->foreign('parameter_id')->references('id')->on('parameter_definitions')->restrictOnDelete();
            $t->json('value')->nullable();
            $t->string('reason');
            $t->timestamps();
            $t->unique(['flow_id', 'parameter_id']);
        });
        Schema::create('translations', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('scope');
            $t->string('key');
            $t->string('locale', 20);
            $t->text('source_text');
            $t->string('source_hash', 64);
            $t->text('text')->nullable();
            $t->string('status')->default('missing');
            $t->boolean('critical')->default(true);
            $t->foreignId('reviewed_by')->nullable()->constrained('users');
            $t->string('approved_source_hash', 64)->nullable();
            $t->timestamp('reviewed_at')->nullable();
            $t->unsignedInteger('revision')->default(1);
            $t->timestamps();
            $t->unique(['scope', 'key', 'locale']);
        });
        Schema::create('assessment_reports', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('run_id')->references('id')->on('assessment_runs')->restrictOnDelete();
            $t->unsignedInteger('input_revision');
            $t->string('fingerprint', 64)->unique();
            $t->uuid('baseline_id')->nullable();
            $t->foreignUuid('price_book_id')->nullable()->constrained('price_books')->restrictOnDelete();
            $t->json('snapshot');
            $t->json('output');
            $t->json('internal_trace')->nullable();
            $t->string('status');
            $t->timestamp('valid_until')->nullable();
            $t->timestamps();
        });
        Schema::create('idempotencies', function (Blueprint $t) {
            $t->id();
            $t->string('scope', 64);
            $t->string('key_hash', 64);
            $t->string('payload_hash', 64);
            $t->json('response')->nullable();
            $t->timestamp('expires_at');
            $t->timestamps();
            $t->unique(['scope', 'key_hash']);
        });
        Schema::create('audit_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('actor_id')->nullable()->constrained('users');
            $t->string('action');
            $t->string('resource_id')->nullable();
            $t->json('metadata');
            $t->uuid('correlation_id');
            $t->timestamps();
        });
        Schema::create('analytics_events', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('event');
            $t->uuid('run_id')->nullable();
            $t->string('flow_slug')->nullable();
            $t->string('locale', 20)->nullable();
            $t->json('dimensions');
            $t->timestamps();
        });
        Schema::create('support_requests', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('run_id')->nullable()->references('id')->on('assessment_runs')->nullOnDelete();
            $t->string('principal_hash', 64);
            $t->string('type');
            $t->text('detail')->nullable();
            $t->string('status')->default('requested');
            $t->boolean('consent')->default(false);
            $t->foreignId('assigned_to')->nullable()->constrained('users');
            $t->timestamps();
        });
        Schema::create('export_jobs', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignUuid('report_id')->references('id')->on('assessment_reports')->cascadeOnDelete();
            $t->string('format');
            $t->string('status')->default('queued');
            $t->string('path')->nullable();
            $t->string('error_code')->nullable();
            $t->unsignedInteger('attempts')->default(0);
            $t->timestamps();
            $t->unique(['report_id', 'format']);
        });
        Schema::create('jobs', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('queue')->index();
            $t->longText('payload');
            $t->unsignedTinyInteger('attempts');
            $t->unsignedInteger('reserved_at')->nullable();
            $t->unsignedInteger('available_at');
            $t->unsignedInteger('created_at');
        });
        Schema::create('failed_jobs', function (Blueprint $t) {
            $t->id();
            $t->string('uuid')->unique();
            $t->text('connection');
            $t->text('queue');
            $t->longText('payload');
            $t->longText('exception');
            $t->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        foreach (['failed_jobs', 'jobs', 'export_jobs', 'support_requests', 'analytics_events', 'audit_events', 'idempotencies', 'assessment_reports', 'translations', 'parameter_overrides', 'parameter_definitions', 'catalog_items', 'price_books', 'answer_revisions', 'assessment_runs', 'flow_releases', 'flows', 'password_reset_tokens', 'users'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
