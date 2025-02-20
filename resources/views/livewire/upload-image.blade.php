<div>
    <x-filament::modal id="filament-upload-image" width="lg">
        <x-slot name="trigger">
            <x-filament::button icon="heroicon-m-arrow-up-tray" color="gray">{{__('image-manager::actions.upload')}}</x-filament::button>
        </x-slot>
        
        <div class="flex flex-col gap-4">
            <div class="flex gap-4">
                <div class="w-32 h-32 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 text-sm border text-center">
                    @if ($this->isPreviewable())
                        <img src="{{ $image->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-lg">
                    @else
                        {{__('image-manager::upload.no_preview')}}
                    @endif
                </div>
                <div class="flex justify-center flex-col gap-2 w-full" @click="$refs.fileInput.click()" x-data="{ fileName: '' }">
                    <input type="file" x-ref="fileInput" wire:model="image" class="w-full !outline-none hidden" @change="fileName = $refs.fileInput.files[0]?.name">
                    <x-filament::button wire:loading.remove wire:target="image" icon="heroicon-m-arrow-up-tray" outlined size="sm">{{__('image-manager::actions.choose_file')}}</x-filament::button>
                    <div x-show="fileName" wire:loading.class="hidden" wire:target="image" class="text-sm">{{__('image-manager::upload.selected')}} <strong x-text="fileName.substring(0,50)" class="line-clamp-2"></strong> ({{__('image-manager::upload.click_to_change')}})</div>
                    <div wire:loading wire:target="image" class="text-sm">
                        <div class="flex gap-2 items-center"><x-filament::loading-indicator class="h-5 w-5 break-normal" /> {{__('image-manager::upload.uploading')}}</div>
                        <strong x-text="fileName.substring(0,50)" class="line-clamp-2"></strong>
                    </div>
                </div>
            </div>

            @error('image')
            <p class="fi-fo-field-wrp-error-message text-sm text-danger-600 dark:text-danger-400">
                {{ $message }}
            </p>
            @enderror

            <div class="fi-ip-button-group flex gap-2">
                <x-filament::button color="gray" wire:click="$cancelUpload('image');$wire.$refresh();$dispatch('close-modal',{id:'filament-upload-image'})">
                    {{__('image-manager::actions.cancel')}}
                </x-filament::button>
                <x-filament::button wire:loading wire:target="image">
                    {{__('image-manager::upload.uploading_button')}}
                </x-filament::button>
                <x-filament::button wire:click="finalizeUpload" wire:loading.remove wire:target="image">
                    {{__('image-manager::actions.finalize')}}
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>
</div>
