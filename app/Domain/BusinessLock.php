<?php

namespace App\Domain;

use Illuminate\Support\Facades\DB;

final class BusinessLock
{
    public static function acquire(): void
    {
        // PostgreSQL locks this row. SQLite reserves the writer before reading:
        // native BEGIN IMMEDIATE on PHP8.4+, a tracked mutex write on older PHP.
        if (! DB::table('business_locks')->where('id', 1)->lockForUpdate()->first()) {
            throw new \RuntimeException('Business publication lock is unavailable.');
        }
    }
}
