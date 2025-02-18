<?php

namespace NSchouten\FilamentImageManager\Filament\Resources;

use NSchouten\FilamentImageManager\Filament\Resources\ImagesResource\Pages;
use NSchouten\FilamentImageManager\Filament\Resources\ImagesResource\RelationManagers;
use NSchouten\FilamentImageManager\Models\Image;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImagesResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static ?string $model = Image::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('path')
                                           ->label(__('image-manager::form.image'))
                                           ->required()
                                           ->image()
                                           ->imageEditor()
                                           ->disk( config('image-manager.disk') )
                                           ->directory( config('image-manager.directory') )
                                           ->columnSpanFull()
                                           ->storeFileNamesIn('name'),
                Forms\Components\TextInput::make('name')
                                          ->label(__('image-manager::form.name'))
                                          ->required()
                                          ->columnSpanFull()
                                          ->minLength(5)
                                          ->maxLength(150),
                Forms\Components\TextInput::make('alt')
                                          ->label(__('image-manager::form.alt'))
                                          ->minLength(5)
                                          ->maxLength(150),
                Forms\Components\TextInput::make('title')
                                          ->label(__('image-manager::form.title'))
                                          ->minLength(5)
                                          ->maxLength(150),
                Forms\Components\TextInput::make('copyright.author')
                                          ->label(__('image-manager::form.author'))
                                          ->minLength(5)
                                          ->maxLength(150),
                Forms\Components\TextInput::make('copyright.source')
                                          ->label(__('image-manager::form.source'))
                                          ->url()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('url')
                                          ->label(__('image-manager::form.image'))
                                          ->height(100)
                                          ->width(100)
                                          ->state(function (Image $image): string {
                                                return $image->getConversion('square');
                                            }),
                Tables\Columns\TextColumn::make('name')
                                         ->limit(50)
                                         ->label(__('image-manager::form.name'))
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $eloquent = parent::getEloquentQuery();
        if( config('image-manager.diskForced') === true ) {
            $eloquent = $eloquent->where('disk', config('image-manager.disk'));
        }
        if( config('image-manager.directoryForced') === true ) {
            $eloquent = $eloquent->where('directory', config('image-manager.directory'));
        }
        return $eloquent;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImages::route('/'),
            'create' => Pages\CreateImages::route('/create'),
            'edit' => Pages\EditImages::route('/{record}/edit'),
        ];
    }

    public static function getModel(): string
    {
        return config('image-manager.model') ?? Image::class;
    }

    public static function getLabel(): string
    {
        return __('image-manager::resources.images.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('image-manager::resources.images.plural');
    }
}
