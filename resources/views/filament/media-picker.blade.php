<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}'), imageSrc: '{{$getImage($getState())}}' }" id="image_picker_{{$field->getId()}}">
        <input x-model="state" hidden/>
        <div class="flex gap-2 items-center">
            <div x-show="imageSrc" class="flex relative">
                <img :src="imageSrc" loading="lazy" class="h-32 w-32 object-cover rounded-lg border"/>
                <div class="absolute p-2">
                    <x-filament::icon-button icon="heroicon-m-trash" size="sm" color="danger" label="{{__('image-manager::actions.remove')}}" @click="imageSrc=null;state=null"/>
                </div>
            </div>
            <div class="flex gap-2" :class="imageSrc ? 'flex-col' : 'flex-row'">
                <livewire:image-manager-image-selector wire:key="is_{{$field->getId()}}" :field="$field->getId()" :disk="$getDisk()" :directory="$getDirectory()" :diskForced="$getDiskForced()" :directoryForced="$getDirectoryForced()"/>
                @can('create', \NSchouten\FilamentImageManager\Models\Image::class)
                    <livewire:image-manager-upload-image wire:key="ui_{{$field->getId()}}" :field="$field->getId()" :disk="$getDisk()" :directory="$getDirectory()" :diskForced="$getDiskForced()" :directoryForced="$getDirectoryForced()"/>
                @endcannot
            </div>
        </div>
    </div>
</x-dynamic-component>

@script
<script>
    $wire.on('select-image', (event) => {
        Alpine.$data( document.getElementById('image_picker_'+event.field) ).state = event.id
        Alpine.$data( document.getElementById('image_picker_'+event.field) ).imageSrc = event.url
    });
</script>
@endscript
