<?php

return ['default' => env('DB_CONNECTION', 'sqlite'), 'connections' => [
    'sqlite' => ['driver' => 'sqlite', 'url' => env('DB_URL'), 'database' => env('DB_DATABASE', database_path('database.sqlite')), 'prefix' => '', 'foreign_key_constraints' => true, 'busy_timeout' => 5000, 'journal_mode' => 'WAL', 'synchronous' => 'NORMAL', 'transaction_mode' => 'IMMEDIATE'],
    'pgsql' => ['driver' => 'pgsql', 'url' => env('DB_URL'), 'host' => env('DB_HOST', '127.0.0.1'), 'port' => env('DB_PORT', '5432'), 'database' => env('DB_DATABASE', 'waypoint'), 'username' => env('DB_USERNAME'), 'password' => env('DB_PASSWORD'), 'charset' => 'utf8', 'prefix' => '', 'search_path' => 'public', 'sslmode' => 'prefer']],
    'migrations' => ['table' => 'migrations', 'update_date_on_publish' => true]];
