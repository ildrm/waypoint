<?php

namespace App\Providers;

use App\Database\SerializingSQLiteConnection;
use App\Domain\DictionaryLoader;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Connection::resolverFor('sqlite', fn ($pdo, $database, $prefix, $config) => new SerializingSQLiteConnection($pdo, $database, $prefix, $config));
        $this->app->extend('translation.loader', fn ($loader) => new DictionaryLoader($loader));
    }

    public function boot(): void {}
}
