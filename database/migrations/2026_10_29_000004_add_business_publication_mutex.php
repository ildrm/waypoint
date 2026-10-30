<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_locks', function (Blueprint $t) {
            $t->unsignedInteger('id')->primary();
        });
        DB::table('business_locks')->insert(['id' => 1]);
    }

    public function down(): void
    {
        Schema::dropIfExists('business_locks');
    }
};
