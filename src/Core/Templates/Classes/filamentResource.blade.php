<?php
/**
 * This is the template for generating the service class of a specified table.
 */

/* @var \Chatway\LaravelCrudGenerator\Core\Generators\DtoGenerator $generator  */
/* @var $generator->generatorForm->properties array list of properties (property => [type, name. comment]) */

echo "<?php\n";
?>

namespace {{ class_namespace($generator->scenarioValue('filamentResourceName')) }};

@if ($generator->baseClass)
use {{ $generator->baseClass }};
@endif
use {{ $generator->scenarioValue('manageFilamentName') }};
use {{ $generator->scenarioValue('modelName') }};
use {{ $generator->scenarioValue('dtoName') }};
use {{ $generator->scenarioValue('serviceName') }};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Carbon;
use Filament\Forms\Form;
use Spatie\LaravelData\Optional;
use Filament\Tables;

class {{ class_basename($generator->scenarioValue('filamentResourceName')) }}{{ $generator->baseClass ? (' extends ' . class_basename($generator->baseClass)) : '' }}
{
    protected static ?string $model = {{ class_basename($generator->scenarioValue('modelName')) }}::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('admin.menu.');
    }

    public static function getModelLabel(): string
    {
        return __('admin.menu.');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }
    public static function columns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')
                ->sortable()
                ->limit(50)
                ->label(__('admin.columns.title')),
            Tables\Columns\TextColumn::make('published_at')
                ->dateTime('d.m.Y H:i', 'Europe/Moscow')
                ->sortable('published_at')
                ->label(__('admin.columns.published_at')),
            Tables\Columns\SelectColumn::make('status')
                ->rules(['required'])
                ->selectablePlaceholder(false)
                ->options(DefaultStatusEnum::labels())
                ->label(__('admin.columns.status')),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
    return parent::getEloquentQuery()
        ->withoutGlobalScope(SoftDeletingScope::class)
        ->when(!in_array(request()->get('tableSortColumn'), ['null', null]), fn(Builder $query) => $query);
    }

    public static function getPages(): array
    {
        return [
            'index' => {{ class_basename($generator->scenarioValue('manageFilamentName')) }}::route('/'),
        ];
    }

    public static function update(Model $record, array $state): void
    {
        app({{ class_basename($generator->scenarioValue('serviceName')) }}::class)->update($record, {{ class_basename($generator->scenarioValue('dtoName')) }}::from([
@foreach($generator->generatorForm->properties as $property)
@if(in_array($property->name, ['created_at', 'updated_at', 'deleted_at', 'id']))@continue @endif()
            '{{ Str::camel($property->name) }}' => data_get($state, '{{ $property->name }}', Optional::create()),
@endforeach()
        ]));
    }
}
