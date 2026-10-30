<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->foreignId('user_id')->nullable()->constrained('users');
            $t->string('principal_hash', 64)->index();
            $t->string('name')->default('Assessment context');
            $t->timestamps();
        });
        Schema::table('assessment_runs', fn (Blueprint $t) => $t->foreignUuid('project_id')->nullable()->constrained('projects')->restrictOnDelete());
        foreach (DB::table('assessment_runs')->get() as $run) {
            $id = (string) Str::uuid();
            DB::table('projects')->insert(['id' => $id, 'user_id' => $run->user_id, 'principal_hash' => $run->principal_hash, 'name' => 'Assessment context', 'created_at' => now(), 'updated_at' => now()]);
            DB::table('assessment_runs')->where('id', $run->id)->update(['project_id' => $id]);
        }Schema::table('flows', fn (Blueprint $t) => $t->uuid('review_evidence_id')->nullable());
        Schema::create('operations', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('kind');
            $t->string('status');
            $t->uuid('resource_id')->nullable();
            $t->foreignId('user_id')->nullable()->constrained('users');
            $t->string('principal_hash', 64);
            $t->string('result_url')->nullable();
            $t->string('error_code')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operations');
        Schema::table('flows', fn (Blueprint $t) => $t->dropColumn('review_evidence_id'));
        Schema::table('assessment_runs', fn (Blueprint $t) => $t->dropConstrainedForeignId('project_id'));
        Schema::dropIfExists('projects');
    }
};
