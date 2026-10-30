<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['flows', 'price_books', 'catalog_items', 'translations'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->foreignId('last_edited_by')->nullable()->constrained('users');
            });
        }foreach (['price_books', 'catalog_items', 'users'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->unsignedInteger('revision')->default(1));
        }
    }

    public function down(): void
    {
        foreach (['flows', 'price_books', 'catalog_items', 'translations'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropConstrainedForeignId('last_edited_by'));
        }foreach (['price_books', 'catalog_items', 'users'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->dropColumn('revision'));
        }
    }
};
