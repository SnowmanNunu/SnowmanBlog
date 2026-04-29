<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- 分组缓存管理 --}}
        <x-filament::section>
            <x-slot name="heading">
                分组缓存
            </x-slot>
            <x-slot name="description">
                选择需要清除的缓存分组，仅清除应用内的 tagged 缓存。
            </x-slot>

            <div class="space-y-3">
                @foreach ($this->getCacheGroups() as $key => $group)
                    <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <input type="checkbox" wire:model="selectedCaches" value="{{ $key }}" class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                @svg($group['icon'], 'w-5 h-5 text-' . $group['color'] . '-500')
                                <span class="font-medium text-gray-900 dark:text-white">{{ $group['label'] }}</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $group['description'] }}</p>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="mt-4 flex gap-3">
                <x-filament::button wire:click="clearSelectedCaches" color="danger" icon="heroicon-m-trash">
                    清除选中缓存
                </x-filament::button>
                <x-filament::button wire:click="clearAllCaches" color="gray" icon="heroicon-m-bolt-slash">
                    清除全部缓存
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- 系统缓存操作 --}}
        <x-filament::section>
            <x-slot name="heading">
                系统缓存
            </x-slot>
            <x-slot name="description">
                Laravel 框架级别的缓存操作，包括路由、视图、配置等。
            </x-slot>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-filament::button wire:click="clearRouteCache" color="info" icon="heroicon-m-map">
                    清除路由缓存
                </x-filament::button>

                <x-filament::button wire:click="clearViewCache" color="info" icon="heroicon-m-eye">
                    清除视图缓存
                </x-filament::button>

                <x-filament::button wire:click="clearConfigCache" color="info" icon="heroicon-m-cog-6-tooth">
                    清除配置缓存
                </x-filament::button>

                <x-filament::button wire:click="optimizeApp" color="success" icon="heroicon-m-rocket-launch">
                    一键优化应用
                </x-filament::button>
            </div>

            <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                <div class="flex gap-2 text-amber-800 dark:text-amber-200 text-sm">
                    @svg('heroicon-m-exclamation-triangle', 'w-5 h-5 flex-shrink-0')
                    <p>生产环境建议谨慎使用"清除全部缓存"，大量缓存重建可能导致短暂性能下降。</p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
