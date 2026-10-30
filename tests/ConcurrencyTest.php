<?php

namespace Tests;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

final class ConcurrencyTest extends TestCase
{
    public function test_two_real_sqlite_processes_serialize_business_writes_before_reading(): void
    {
        $directory = storage_path('framework/testing/concurrency-'.Str::uuid());
        mkdir($directory, 0700, true);
        $database = $directory.'/isolated.sqlite';
        $marker = $directory.'/holder.locked';
        $pdo = new \PDO('sqlite:'.$database);
        $pdo->exec('CREATE TABLE business_locks (id INTEGER PRIMARY KEY); INSERT INTO business_locks VALUES (1); CREATE TABLE counters (id INTEGER PRIMARY KEY, value INTEGER); INSERT INTO counters VALUES (1,0)');
        $pdo = null;
        $code = <<<'PHP'
require $argv[1].'/vendor/autoload.php';
$app=require $argv[1].'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default'=>'sqlite','database.connections.sqlite.database'=>$argv[2]]);
\Illuminate\Support\Facades\DB::purge('sqlite');
if(config('database.connections.sqlite.transaction_mode')!=='IMMEDIATE')throw new \RuntimeException('Write mode not configured');
if($argv[4]==='contender')file_put_contents($argv[3].'.contender-ready','ready');
$started=microtime(true);
\Illuminate\Support\Facades\DB::transaction(function()use($argv){
    \App\Domain\BusinessLock::acquire();
    $before=\Illuminate\Support\Facades\DB::table('counters')->where('id',1)->value('value');
    if($argv[4]==='holder'){
        file_put_contents($argv[3],'locked');$until=microtime(true)+8;
        while(!file_exists($argv[3].'.contender-ready')&&microtime(true)<$until)usleep(10000);
        if(!file_exists($argv[3].'.contender-ready'))throw new \RuntimeException('Contender did not reach transaction');
        usleep(250000);
    }
    \Illuminate\Support\Facades\DB::table('counters')->where('id',1)->update(['value'=>$before+1]);
});
echo 'committed:'.(microtime(true)-$started);
PHP;
        $environment = ['APP_ENV' => 'testing', 'DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database, 'LOG_CHANNEL' => 'stderr', 'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array'];
        $holder = new Process([PHP_BINARY, '-r', $code, base_path(), $database, $marker, 'holder'], base_path(), $environment);
        $contender = new Process([PHP_BINARY, '-r', $code, base_path(), $database, $marker, 'contender'], base_path(), $environment);
        $holder->setTimeout(15);
        $contender->setTimeout(15);
        try {
            $holder->start();
            $deadline = microtime(true) + 10;
            while (! file_exists($marker) && $holder->isRunning() && microtime(true) < $deadline) {
                usleep(10000);
            }
            $this->assertFileExists($marker, $holder->getErrorOutput());
            $contender->start();
            $holder->wait();
            $contender->wait();
            $this->assertTrue($holder->isSuccessful(), $holder->getErrorOutput());
            $this->assertTrue($contender->isSuccessful(), $contender->getErrorOutput());
            $this->assertStringContainsString('committed', $holder->getOutput());
            $this->assertStringContainsString('committed', $contender->getOutput());
            preg_match('/committed:([0-9.]+)/', $contender->getOutput(), $elapsed);
            $this->assertGreaterThan(0.15, (float) ($elapsed[1] ?? 0));
            $pdo = new \PDO('sqlite:'.$database);
            $this->assertSame(2, (int) $pdo->query('SELECT value FROM counters WHERE id=1')->fetchColumn());
            $pdo = null;
        } finally {
            $holder->stop();
            $contender->stop();
            foreach (glob($directory.'/*') as $file) {
                unlink($file);
            }
            rmdir($directory);
        }
    }
}
