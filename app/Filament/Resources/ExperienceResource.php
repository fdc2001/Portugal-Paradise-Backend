<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExperienceResource\Pages;
use App\Filament\Resources\Settings\ExperiencePartnerResource;
use App\Filament\Resources\Settings\ExperienceServiceResource;
use App\Models\Experience;
use App\Models\Settings\ExperiencePartner;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\Url;
use SolutionForest\FilamentTranslateField\Forms\Component\Translate;

class ExperienceResource extends Resource
{
    protected static ?string $model = Experience::class;

    protected static ?string $slug = 'experiences';

    protected static ?string $navigationIcon = 'heroicon-o-gift-top';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                ->schema([
                    Section::make(__('filament.experience.experience_details'))
                        ->columns(3)
                        ->columnSpan(4)
                        ->schema([
                            Select::make('experience_type_id')
                                ->createOptionForm(function (Form $form){
                                    return ExperiencePartnerResource::form($form);
                                })
                                ->required()
                                ->preload()
                                ->label(__('filament.experience.experience_type'))
                                ->searchable()
                                ->relationship('experienceType', 'name'),
                            Select::make('experience_partner_id')
                                ->label(__('filament.experience.experience_partner'))
                                ->createOptionForm(function (Form $form){
                                    return ExperiencePartnerResource::form($form);
                                })
                                ->required()
                                ->preload()
                                ->searchable()
                                ->relationship('experiencePartner', 'name'),
                            TextInput::make('min_guests')
                                ->label(__('filament.experience.min_guests'))
                                ->required()
                                ->integer(),
                            Select::make('services')
                                ->createOptionForm(function (Form $form){
                                    return ExperienceServiceResource::form($form);
                                })
                                ->relationship('services', 'name')
                                ->multiple()
                                ->preload()
                                ->columnSpanFull()
                                ->label(__('filament.experience.services')),
                            Grid::make()
                                ->schema([
                                    TextInput::make('adult_price')
                                        ->integer()
                                        ->suffixIcon('heroicon-o-currency-euro'),
                                    TextInput::make('child_price')
                                        ->integer()
                                        ->suffixIcon('heroicon-o-currency-euro'),
                                ]),
                            Translate::make()
                                ->columnSpanFull()
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('filament.experience.name')),
                                    RichEditor::make('description')->nullable(),
                                    RichEditor::make('additional_info')->nullable()
                                ]),
                        ]),
                    Section::make(__('filament.experience.experience_image'))
                        ->columnSpan(4)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('images')
                            ->multiple()
                            ->label("")
                            ->columnSpanFull()
                            ->reorderable()
                    ])
                ]),
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Experience $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Experience $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')
                    ->label(__('filament.experience.experience_image'))
                    ->limit(1),
                TextColumn::make('experienceType.name')
                    ->label(__('filament.experience.experience_type')),

                TextColumn::make('name')
                    ->label(__('filament.experience.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('experiencePartner.name')
                    ->label(__('filament.experience.experience_partner')),

                TextColumn::make('min_guests')
                    ->label(__('filament.experience.min_guests')),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('experience_type_id')
                    ->relationship('experienceType', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('filament.experience.experience_type')),
                SelectFilter::make('experience_partner_id')
                    ->relationship('experiencePartner', 'name')
                    ->multiple()
                    ->preload()
                    ->label(__('filament.experience.experience_partner')),
            ])
            ->actions([
                EditAction::make()->slideOver(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExperiences::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
