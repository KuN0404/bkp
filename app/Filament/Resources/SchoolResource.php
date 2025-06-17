<?php

namespace App\Filament\Resources;

use App\Models\School;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\SchoolResource\Pages;
use App\Filament\Resources\SchoolResource\Pages\EditSchool;
use App\Filament\Resources\SchoolResource\Pages\ListSchools;
use App\Filament\Resources\SchoolResource\Pages\CreateSchool;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Data Sekolah'; // <-- Mengubah nama di sidebar

    protected static ?string $modelLabel = 'Sekolah'; // <-- Label untuk satu data
    protected static ?string $pluralModelLabel = 'Daftar Sekolah'; // <-- Judul di halaman utama resource


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('subdistrict_id') // Sesuaikan dengan nama kolom yang benar
                    ->relationship('subdistrict', 'subdistrict_name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('school_type')
                    ->options([
                        'SD' => 'Sekolah Dasar (SD)',
                        'SMP' => 'Sekolah Menengah Pertama (SMP)',
                        'SMA' => 'Sekolah Menengah Atas (SMA)',])
                    ->label('Tingkat Sekolah')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('school_status')
                    ->options([
                        'Negeri' => 'Negeri',
                        'Swasta' => 'Swasta',])
                    ->label('Jenis Sekolah')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('school_name')->label('Nama Sekolah')->required(),
                TextInput::make('principal_name')->label('Nama Kepala Sekolah')->nullable(),
                TextInput::make('principal_nip')->label('NIP Kepala Sekolah')->nullable(),
                TextInput::make('treasurer_name')->label('Nama Bendahara')->nullable(),
                TextInput::make('treasurer_nip')->label('NIP Bendahara')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subdistrict.subdistrict_name')->label('Kecamatan')->sortable(),
                TextColumn::make('school_type')->label('Tingkat Sekolah')->searchable()->sortable(),
                TextColumn::make('school_status')->label('Status Sekolah')->searchable()->sortable(),
                TextColumn::make('school_name')->label('Nama Sekolah')->searchable()->sortable(),
                TextColumn::make('principal_name')->label('Kepala Sekolah')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('treasurer_name')->label('Bendahara')->searchable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('subdistrict')->relationship('subdistrict', 'subdistrict_name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('subdistrict.subdistrict_name')
                ->label('Kecamatan'),
                TextEntry::make('school_type')
                ->label('Tingkat Sekolah'),
                TextEntry::make('school_status')
                ->label('Status Sekolah'),
                TextEntry::make('school_name')
                ->label('Sekolah'),
                ViewEntry::make('principal_name')
                ->label('Kepala Sekolah')
                ->view('display-or-dash'),
                ViewEntry::make('principal_nip')
                ->label('NIP Kepala Sekolah')
                ->view('display-or-dash'),
                ViewEntry::make('treasurer_name')
                ->label('Bendahara')
                ->view('display-or-dash'),
                ViewEntry::make('treasurer_nip')
                ->label('NIP Bendahara')
                ->view('display-or-dash'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'view' => Pages\ViewSchool::route('/{record}'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}
