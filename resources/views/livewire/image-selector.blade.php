<div>
    <x-filament::modal id="filament-select-image" width="3xl">
        <x-slot name="trigger">
            <x-filament::button icon="heroicon-m-photo">{{__('image-manager::actions.select')}}</x-filament::button>
        </x-slot>
        <x-slot name="heading">
            {{__('image-manager::select.title')}}
        </x-slot>
        <x-slot name="description">
        {{__('image-manager::select.description')}}
        </x-slot>
        
        <div class="flex flex-col gap-4">
            <div class="flex gap-2 flex-wrap justify-center rounded-lg p-4 max-h-96 overflow-auto">
                @if($images->isEmpty())
                    {{__('image-manager::select.non_found')}}
                @else
                    @foreach($images as $image)
                        <div class="rounded-lg w-32 h-32 overflow-hidden cursor-pointer" @click="$dispatch('close-modal',{id:'filament-select-image'});$dispatch('select-image',{field:'{{$this->field}}',url:'{{$image->url}}',id:'{{$image->id}}' })">
                            <img src="{{$image->getConversion('square')}}" class="w-full h-full object-cover "/>
                        </div>
                    @endforeach
                @endif
            </div>
            <x-filament::pagination :paginator="$images" :page-options="[5, 10, 20]" current-page-option-property="perPage"/>
        </div>
    </x-filament::modal>
</div>
