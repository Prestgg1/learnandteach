<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
  {
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                ->label('Title')
                ->required(),
                TextInput::make('author')->default(\Illuminate\Support\Facades\Auth::user()->id)
                ->label('User')
                ->required(),
                RichEditor::make('content')->columnSpanFull()
                ->label('Content')
                ->required(),
                SpatieMediaLibraryFileUpload::make('thumbnail')->collection('blogs')->label('Thumbnail'),
                SpatieTagsInput::make('tags'),
             
               /*  Select::make('category_id')
                ->label('Category')
                ->relationship('category')
                ->required(), */
            /*   Select::make('category_id')
                ->label('Category')
              ->relationship('category', 'name->tr')
                ->required(),
                SpatieTagsInput::make('tags'), */
              Toggle::make('active')
                ->label('Is Active')
                ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('author')->sortable()->searchable(),
                SpatieMediaLibraryImageColumn::make('thumbnail')->collection('blogs')->label('Thumbnail'),
                TextColumn::make('slug')->sortable()->searchable(),
                TextColumn::make('tags')->searchable()
                ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
