<?php

namespace App\Filament\Resources;

use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Activity;
use Filament\Forms\Form;
use Filament\Tables\Table;
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
use Filament\Tables\Filters\TrashedFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\Pages\EditActivity;
use App\Filament\Resources\ActivityResource\Pages\ViewActivity;
use App\Filament\Resources\ActivityResource\Pages\CreateActivity;
use App\Filament\Resources\ActivityResource\Pages\ListActivities;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Kegiatan';
    protected static ?string $navigationLabel = 'Data Kegiatan'; // <-- Mengubah nama di sidebar
    protected static ?string $pluralModelLabel = 'Daftar Kegiatan'; // <-- Judul di halaman utama resource


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('activity_type')
                    ->label('Pilih Tingkat Kegiatan')
                    ->options([
                        'SD' => 'Sekolah Dasar (SD)',
                        'SMP' => 'Sekolah Menengah Pertama (SMP)',
                        'SMA' => 'Sekolah Menengah Atas (SMA)',
                    ])
                    ->default('SD') // Set default value to 'SD'
                    ->searchable()
                    ->required(),
                TextInput::make('activity_name')
                    ->label('Nama Kegiatan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('director_name')
                    ->label('Nama Penanggung Jawab')
                    ->required()
                    ->maxLength(255),
                TextInput::make('dpp')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->live(onBlur: true) // Memicu update saat user pindah field
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotal($get, $set)),

                TextInput::make('ppn')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotal($get, $set)),

                TextInput::make('pph')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotal($get, $set)),

                TextInput::make('total')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly() // <-- KUNCI UTAMA: Field ini tidak bisa diisi manual
                    ->required(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('activity_type')
                    ->label('Tingkat Kegiatan')
                    ->badge()
                    ->color(function (activity $record): string {
                        if ($record->activity_type == "SD") {
                            return 'danger';
                        } elseif($record->activity_type == "SMP") {
                            return 'primary';
                        }else{
                            return 'info';
                        }
                    })
                    ->icon('heroicon-o-academic-cap'),
                TextEntry::make('activity_name')->label('Nama Kegiatan'),
                TextEntry::make('director_name')->label('Penanggung Jawab'),
                TextEntry::make('dpp')->label('Total DPP')->money('IDR'),
                TextEntry::make('ppn')->label('Total PPN')->money('IDR'),
                TextEntry::make('pph')->label('Total PPh')->money('IDR'),
                TextEntry::make('total')->label('Total Biaya')->money('IDR'),
                TextEntry::make('deleted_at')
                    ->label('Status')
                    ->state(function (Activity $record): string {
                        if ($record->deleted_at === null) {
                            return 'Aktif';
                        } else {
                            return 'Tidak Aktif';
                        }
                    })
                    ->color(function (Activity $record): string {
                        if ($record->deleted_at === null) {
                            return 'success'; // Green for active records
                        } else {
                            return 'danger'; // Red for deleted records
                        }
                    })
                    ->icon(function (Activity $record): ?string {
                        if ($record->deleted_at === null) {
                            return 'heroicon-o-check-circle'; // Icon for active records
                        } else {
                            return 'heroicon-o-x-circle'; // Icon for deleted records
                        }
                    })
                    ->badge(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activity_type')
                    ->label('Tingkat Kegiatan')
                    ->searchable()
                    ->badge()
                    ->color(function (activity $record): string {
                        if ($record->activity_type == "SD") {
                            return 'danger';
                        } elseif($record->activity_type == "SMP") {
                            return 'primary';
                        }else{
                            return 'info';
                        }
                    })
                    ->icon('heroicon-o-academic-cap'),
                TextColumn::make('activity_name')->label('Nama Kegiatan')->searchable(),
                TextColumn::make('director_name')->label('Penanggung Jawab')->searchable(),
                TextColumn::make('total')->label('Total Biaya')->money('IDR')->sortable(),
                TextColumn::make('deleted_at')
                    ->label('Status')
                    ->state(function (Activity $record): string {
                        if ($record->deleted_at === null) {
                            return 'Aktif';
                        } else {
                            return 'Tidak Aktif';
                        }
                    })
                    ->color(function (Activity $record): string {
                        if ($record->deleted_at === null) {
                            return 'success'; // Green for active records
                        } else {
                            return 'danger'; // Red for deleted records
                        }
                    })
                    ->icon(function (Activity $record): ?string {
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
            ])
            ->actions([
                ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation()
                    ,
                Action::make('restore')
                    ->label('Pulihkan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Activity $record) {
                        $record->restore();
                    })
                    ->hidden(fn (Activity $record) => $record->deleted_at === null),
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

    /**
     * Helper function untuk menghindari duplikasi kode.
     * Fungsi ini akan dipanggil setiap kali field dpp, ppn, atau pph berubah.
     */
    public static function updateTotal(Get $get, Set $set): void
    {
        // Mengambil nilai dari form, dan menganggapnya 0 jika kosong
        $dpp = floatval($get('dpp'));
        $ppn = floatval($get('ppn'));
        $pph = floatval($get('pph'));

        // Mengatur nilai 'total' berdasarkan hasil penjumlahan
        $set('total', $dpp + $ppn + $pph);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'view' => Pages\ViewActivity::route('/{record}'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
