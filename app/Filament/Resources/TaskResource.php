<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Grid;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationGroup = 'Tarefas';

    protected static ?string $modelLabel = 'tarefa';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                
                    Select::make('user_id')
                        ->label('Usuário')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required(),
                     Select::make('customer_id')
                        ->label('Cliente')
                        ->relationship('customer', 'name')
                        ->searchable()
                        ->required(),
                    Select::make('task_group_id')
                        ->label('Categoria')
                        ->relationship('taskGroup', 'title')
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->columnSpan(2)
                        ->required()
                        ->maxLength(255),
                    RichEditor::make('description')
                        ->label('Descrição')
                        ->columnSpan(2)
                        ->maxLength(65535),
                ])
                ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->limit(10),
                Tables\Columns\TextColumn::make('description')
                    ->limit(20),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->label('Usuário'),

                Tables\Columns\TextColumn::make('customer_id.name')
                    ->label('Cliente')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('taskGroup.title')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable()
                    ->colors([
                        'secondary',
                        'warning' => 'In Progress',
                        'success' => 'Done',
                        'danger' => 'To Do',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/y H:i'),
            ])
            ->filters([
                SelectFilter::make('user')
                ->searchable()
                ->relationship('user', 'name')
                ->label('Usuário'),

                SelectFilter::make('taskGroup')
                ->searchable()
                ->relationship('taskGroup', 'title')
                ->multiple()
                ->label('Grupo da Tarefa')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
