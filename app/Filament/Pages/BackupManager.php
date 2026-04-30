<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationLabel = '备份管理';

    protected static ?string $title = '数据库备份管理';

    protected static ?string $slug = 'backup-manager';

    protected static string $view = 'filament.pages.backup-manager';

    public function getBackups(): array
    {
        $path = storage_path('app/backups');

        if (! is_dir($path)) {
            return [];
        }

        $files = glob($path.'/*.sql');
        usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));

        return array_map(fn ($file) => [
            'name' => basename($file),
            'size' => $this->formatBytes(filesize($file)),
            'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            'path' => $file,
        ], $files);
    }

    public function createBackup(): void
    {
        $result = Artisan::call('backup:database');

        if ($result === 0) {
            Notification::make()
                ->success()
                ->title('备份成功')
                ->body('数据库已备份到本地存储')
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('备份失败')
                ->body('请检查日志获取详细错误信息')
                ->send();
        }
    }

    public function deleteBackup(string $name): void
    {
        $path = storage_path('app/backups/'.$name);

        if (file_exists($path) && unlink($path)) {
            Notification::make()
                ->success()
                ->title('删除成功')
                ->body("备份 {$name} 已删除")
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('删除失败')
                ->send();
        }
    }

    public function downloadBackup(string $name): StreamedResponse
    {
        $path = storage_path('app/backups/'.$name);

        abort_if(! file_exists($path), 404);

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $name);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2).' '.$units[$unitIndex];
    }
}
