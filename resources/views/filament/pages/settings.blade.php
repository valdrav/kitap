<x-filament-panels::page>
    <div class="space-y-8">
        <form wire:submit="updateProfile" class="space-y-6">
            {{ $this->profileForm }}

            <div class="flex justify-end">
                <x-filament::button type="submit" color="primary">
                    Hesabı Kaydet
                </x-filament::button>
            </div>
        </form>

        <form wire:submit="updatePassword" class="space-y-6">
            {{ $this->passwordForm }}

            <div class="flex justify-end">
                <x-filament::button type="submit" color="warning">
                    Şifreyi Değiştir
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
