<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                手动备份
            </x-slot>
            <x-slot name="description">
                立即执行一次数据库备份，备份文件将保存在 storage/app/backups 目录。
                系统也会每天凌晨自动执行备份，并自动清理 30 天前的旧备份。
            </x-slot>

            <x-filament::button wire:click="createBackup" color="primary" icon="heroicon-m-play">
                立即备份
            </x-filament::button>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                备份列表
            </x-slot>
            <x-slot name="description">
                共 {{ count($this->getBackups()) }} 个备份文件
            </x-slot>

            @if (count($backups = $this->getBackups()) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">文件名</th>
                                <th class="px-4 py-3">大小</th>
                                <th class="px-4 py-3">创建时间</th>
                                <th class="px-4 py-3 text-right">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($backups as $backup)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                        {{ $backup['name'] }}
                                    </td>
                                    <td class="px-4 py-3">{{ $backup['size'] }}</td>
                                    <td class="px-4 py-3">{{ $backup['created_at'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('backup.download', ['name' => $backup['name']]) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-500 focus:ring-2 focus:ring-primary-300">
                                                下载
                                            </a>
                                            <x-filament::button
                                                wire:click="deleteBackup('{{ $backup['name'] }}')"
                                                color="danger"
                                                size="xs"
                                                icon="heroicon-m-trash">
                                                删除
                                            </x-filament::button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    暂无备份文件
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
