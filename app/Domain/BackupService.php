<?php

namespace App\Domain;

use Illuminate\Support\Facades\DB;

final class BackupService
{
    public function create(): array
    {
        if (config('database.default') !== 'sqlite') {
            throw new \RuntimeException('Use the reviewed native database backup procedure for this driver.');
        }$dir = storage_path('app/private/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }$path = $dir.'/'.gmdate('Ymd-His').'-'.bin2hex(random_bytes(4)).'.sqlite';
        $pdo = DB::connection()->getPdo();
        $pdo->exec('VACUUM INTO '.$pdo->quote($path));

        return ['path' => $path, 'sha256' => hash_file('sha256', $path), 'integrity' => $this->verify($path)];
    }

    public function verify(string $path): array
    {
        $pdo = new \PDO('sqlite:'.$path);
        $ok = $pdo->query('PRAGMA integrity_check')->fetchColumn();
        if ($ok !== 'ok') {
            throw new \RuntimeException('Backup integrity verification failed.');
        }$counts = [];
        foreach (['flows', 'flow_releases', 'assessment_runs', 'assessment_reports'] as $table) {
            $counts[$table] = (int) $pdo->query('SELECT COUNT(*) FROM '.$table)->fetchColumn();
        }

return $counts;
    }

    public function restoreToNewFile(string $backup, string $filename): array
    {
        if (! preg_match('/^[a-zA-Z0-9_-]{1,80}\.sqlite$/D', $filename)) {
            throw new \InvalidArgumentException('Use a simple new restore filename.');
        }$dir = storage_path('app/private/backups/restores');
        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }$target = $dir.'/'.$filename;
        if (file_exists($target)) {
            throw new \RuntimeException('Restore refuses to overwrite an existing file.');
        }$source = realpath($backup);
        $root = realpath(storage_path('app/private/backups'));
        if (! $source || ! str_starts_with($source, $root.DIRECTORY_SEPARATOR)) {
            throw new \RuntimeException('Choose a local application backup.');
        }$before = $this->verify($source);
        if (! copy($source, $target)) {
            throw new \RuntimeException('Restore copy failed.');
        }$after = $this->verify($target);
        if ($before !== $after) {
            throw new \RuntimeException('Restore row counts differ.');
        }

return ['path' => $target, 'counts' => $after, 'sha256' => hash_file('sha256', $target)];
    }
}
