<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="fi-ac flex gap-3">
            <x-filament::button type="submit" icon="heroicon-m-check-circle">
                保存设置
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
