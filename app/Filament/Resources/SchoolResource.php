<?php

namespace App\Filament\Resources;

use App\Models\School;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subdistrict;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\SchoolResource\Pages;
use App\Filament\Resources\SchoolResource\Pages\EditSchool;
use App\Filament\Resources\SchoolResource\Pages\ViewSchool;
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


    public static function getEloquentQuery(): Builder
{
    // Lakukan eager load pada relasi baru kita
    return parent::getEloquentQuery()->with('subdistrictWithTrashed');
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('subdistrict_id') // Sesuaikan dengan nama kolom yang benar
                    ->options(function () {
                    return Subdistrict::query()
                        ->withTrashed()
                        ->pluck('subdistrict_name', 'id');
                    })
                    ->label('Kecamatan')
                    ->searchable()
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
                TextColumn::make('subdistrictWithTrashed.subdistrict_name')
                    ->label('Kecamatan')
                    ->sortable(),
                TextColumn::make('school_type')
                    ->label('Tingkat Sekolah')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(function (School $record): string {
                        if ($record->school_type == "SD") {
                            return 'danger';
                        } elseif($record->school_type == "SMP") {
                            return 'primary';
                        }else{
                            return 'info';
                        }
                    })
                    ->icon('heroicon-o-academic-cap'),
                TextColumn::make('school_status')
                    ->label('Status Sekolah')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(function (School $record): string {
                        if ($record->school_status == "Negeri") {
                            return 'primary';
                        } else {
                            return 'success';
                        }
                    })
                    ->icon(function (School $record): string {
                        if ($record->school_status == "Negeri") {
                            return 'heroicon-o-building-library';
                        } else {
                            return 'heroicon-o-building-office';
                        }
                    }),
                TextColumn::make('school_name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('principal_name')
                    ->label('Kepala Sekolah')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('treasurer_name')
                    ->label('Bendahara')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Status')
                    ->state(function (School $record): string {
                        if ($record->deleted_at === null) {
                            return 'Aktif';
                        } else {
                            return 'Tidak Aktif';
                        }
                    })
                    ->color(function (School $record): string {
                        if ($record->deleted_at === null) {
                            return 'success'; // Green for active records
                        } else {
                            return 'danger'; // Red for deleted records
                        }
                    })
                    ->icon(function (School $record): ?string {
                        if ($record->deleted_at === null) {
                            return 'heroicon-o-check-circle'; // Icon for active records
                        } else {
                            return 'heroicon-o-x-circle'; // Icon for deleted records
                        }
                    })
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('l, d F Y - H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('subdistrict')->relationship('subdistrict', 'subdistrict_name'),
            ])
            ->actions([
                ActionGroup::make([
                ViewAction::make()
                    ->slideOver(),
                EditAction::make(),
                DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation(),
                Action::make('restore')
                    ->label('Pulihkan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (School $record) {
                        $record->restore();
                    })
                    ->hidden(fn (School $record) => $record->deleted_at === null),
                    ])
                ->dropdown()
                ->label('Aksi')
                ->icon('heroicon-o-ellipsis-horizontal')
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
                TextEntry::make('subdistrictWithTrashed.subdistrict_name')
                ->label('Kecamatan'),
                TextEntry::make('school_type')
                ->label('Tingkat Sekolah')
                ->badge()
                ->color(function (School $record): string {
                    if ($record->school_type == "SD") {
                        return 'danger';
                    } elseif($record->school_type == "SMP") {
                        return 'primary';
                    }else{
                        return 'info';
                    }
                })
                ->icon('heroicon-o-academic-cap'),
                TextEntry::make('school_status')
                ->label('Status Sekolah')
                ->badge()
                ->color(function (School $record): string {
                    if ($record->school_status == "Negeri") {
                        return 'primary';
                    } else {
                        return 'success';
                    }
                })
                ->icon(function (School $record): string {
                    if ($record->school_status == "Negeri") {
                        return 'heroicon-o-building-library';
                    } else {
                        return 'heroicon-o-building-office';
                    }
                }),
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
                TextEntry::make('deleted_at')
                    ->label('Status')
                    ->state(function (School $record): string {
                        if ($record->deleted_at === null) {
                            return 'Aktif';
                        } else {
                            return 'Tidak Aktif';
                        }
                    })
                    ->color(function (School $record): string {
                        if ($record->deleted_at === null) {
                            return 'success'; // Green for active records
                        } else {
                            return 'danger'; // Red for deleted records
                        }
                    })
                    ->icon(function (School $record): ?string {
                        if ($record->deleted_at === null) {
                            return 'heroicon-o-check-circle'; // Icon for active records
                        } else {
                            return 'heroicon-o-x-circle'; // Icon for deleted records
                        }
                    })
                    ->badge(),
                TextEntry::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('l, d F Y - H:i:s'),
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
