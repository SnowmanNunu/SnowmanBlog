<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {--cleanup : 清理 30 天前的备份}';

    protected $description = '备份数据库到本地存储';

    public function handle(): int
    {
        if ($this->option('cleanup')) {
            $this->cleanupOldBackups();

            return self::SUCCESS;
        }

        $connection = config('database.default');
        $filename = sprintf('backup_%s_%s.sql', $connection, now()->format('Y-m-d_H-i-s'));
        $path = storage_path('app/backups/'.$filename);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        try {
            match ($connection) {
                'mysql' => $this->backupMysql($path),
                'sqlite' => $this->backupSqlite($path),
                default => throw new \RuntimeException("不支持的数据库连接: {$connection}"),
            };

            $this->info("数据库备份完成: {$filename}");
            $this->cleanupOldBackups();

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('备份失败: '.$e->getMessage());

            if (file_exists($path)) {
                unlink($path);
            }

            return self::FAILURE;
        }
    }

    private function backupMysql(string $path): void
    {
        $config = config('database.connections.mysql');
        $command = [
            'mysqldump',
            '-h', $config['host'],
            '-P', (string) $config['port'],
            '-u', $config['username'],
        ];

        if (! empty($config['password'])) {
            $command[] = '-p'.$config['password'];
        }

        $command[] = $config['database'];

        $process = new Process($command);
        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        file_put_contents($path, $process->getOutput());
    }

    private function backupSqlite(string $path): void
    {
        $database = config('database.connections.sqlite.database');

        if (! file_exists($database)) {
            throw new \RuntimeException("SQLite 数据库文件不存在: {$database}");
        }

        copy($database, $path);
    }

    private function cleanupOldBackups(): void
    {
        $dir = storage_path('app/backups');

        if (! is_dir($dir)) {
            return;
        }

        $threshold = now()->subDays(30)->getTimestamp();
        $deleted = 0;

        foreach (glob($dir.'/*.sql') as $file) {
            if (filemtime($file) < $threshold) {
                unlink($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("已清理 {$deleted} 个过期备份文件");
        }
    }
}
