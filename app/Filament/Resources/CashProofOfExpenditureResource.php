<?php

namespace App\Filament\Resources;

use App\Models\School;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Activity;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Subdistrict;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use App\Models\CashProofOfExpenditure;
use Illuminate\Support\Facades\Config;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Riskihajar\Terbilang\Facades\Terbilang;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\CashProofOfExpenditureResource\Pages;

use App\Imports\CashProofOfExpendituresImport;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class CashProofOfExpenditureResource extends Resource
{
    protected static ?string $model = CashProofOfExpenditure::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'BKP';
    protected static ?string $modelLabel = 'Bukti Kas Pengeluaran';
    protected static ?string $pluralModelLabel = 'Daftar BKP';

   public static function getEloquentQuery(): Builder
    {
        // PERBAIKAN: Eager load relasi dengan path yang benar dan konsisten
        return parent::getEloquentQuery()->with([
            'schoolWithTrashed.subdistrictWithTrashed', // Memuat subdistrict melalui schoolWithTrashed
            'activityWithTrashed',
        ]);
    }

    public static function form(Form $form): Form
    {
        // Bagian form Anda sudah cukup baik dalam menangani soft delete
        // karena menggunakan kueri langsung dengan withTrashed().
        // Jadi, tidak ada perubahan besar di sini.
        return $form
            ->schema([
                Select::make('subdistrict_id')
                    ->label('Pilih Kecamatan')
                    ->options(Subdistrict::query()->withTrashed()->pluck('subdistrict_name', 'id'))
                    ->live()
                    ->searchable()
                    ->afterStateUpdated(function (Set $set) {
                        $set('school_id', null);
                        $set('activity_id', null);
                        $set('director_name', null);
                    })
                    ->dehydrated(false)
                    ->required(),

                Select::make('school_id')
                    ->label('Sekolah')
                    ->options(function (Get $get): Collection {
                        if (!$get('subdistrict_id')) {
                            return collect();
                        }
                        return School::query()
                            ->where('subdistrict_id', $get('subdistrict_id'))
                            ->withTrashed()
                            ->pluck('school_name', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('activity_id', null);
                        $set('director_name', null);
                    })
                    ->required()
                    ->disabled(fn (Get $get): bool => !$get('subdistrict_id')),

                // Select::make('activity_type')
                //     ->label('Pilih Tipe Kegiatan')
                //     ->options(Activity::query()->withTrashed()->distinct()->pluck('activity_type', 'activity_type'))
                //     ->live()
                //     ->searchable()
                //     ->afterStateUpdated(fn (Set $set) => $set('activity_id', null))
                //     ->dehydrated(false)
                //     ->required(),

                Select::make('activity_id')
                    ->label('Nama Kegiatan')
                    ->options(function (Get $get): Collection {
                        $schoolId = $get('school_id');
                        if (!$schoolId) {
                            return collect(); // Tidak ada sekolah dipilih, tidak ada kegiatan
                        }

                        // Cari tipe sekolah (SD/SMP/SMA) dari sekolah yang dipilih
                        $school = School::withTrashed()->find($schoolId);
                        if (!$school) {
                            return collect();
                        }
                        $schoolType = $school->school_type;

                        // Filter kegiatan berdasarkan tipe sekolah
                        return Activity::query()
                            ->where('activity_type', $schoolType)
                            ->withTrashed()
                            ->pluck('activity_name', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->required()
                    ->disabled(fn (Get $get): bool => !$get('school_id'))
                    ->afterStateUpdated(function ($state, Set $set) {
                        $activity = Activity::withTrashed()->find($state);
                        $set('director_name', $activity?->director_name);
                    })
                    ->required(),

                TextInput::make('director_name')
                    ->label('Nama Direktur')
                    ->readOnly()
                    ->dehydrated(false),

                TextInput::make('number_of_students')
                    ->label('Jumlah Siswa')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateNominalAndSorted($get, $set)),

                TextInput::make('nominal')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly()
                    ->required(),

                Textarea::make('sorted')
                    ->label('Terbilang')
                    ->readOnly()
                    ->required(),
            ]);
    }

    /**
     * Menyiapkan data sebelum form diisi (untuk halaman Edit dan View).
     * Ini akan memastikan dropdown Kecamatan dan Sekolah menampilkan data yang benar.
     */
   public static function mutateFormDataBeforeFill(array $data): array
    {
        // Menggunakan find() pada model utama akan otomatis menangani data yang soft-deleted juga
        $record = CashProofOfExpenditure::find($data['id']);
        if ($record) {
            // PERBAIKAN: Gunakan schoolWithTrashed untuk konsistensi
            $data['subdistrict_id'] = $record->schoolWithTrashed?->subdistrict_id;
            $data['director_name'] = $record->activityWithTrashed?->director_name;
        }
        return $data;
    }

    /**
     * Mendefinisikan layout untuk halaman View.
     */
   public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // PERBAIKAN: Path relasi yang benar
             TextEntry::make('schoolWithTrashed.school_type')
                ->label('Tingkat Sekolah')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'SD' => 'danger',
                    'SMP' => 'primary',
                    default => 'info', // Untuk nilai lainnya atau jika kosong
                })
                    ->icon('heroicon-o-academic-cap'),
                TextEntry::make('schoolWithTrashed.subdistrictWithTrashed.subdistrict_name')->label('Kecamatan'),
                TextEntry::make('schoolWithTrashed.school_name')->label('Sekolah'),
                ViewEntry::make('schoolWithTrashed.principal_name')
                    ->label('Nama Kepala Sekolah')
                    ->view('display-or-dash'),
                ViewEntry::make('schoolWithTrashed.treasurer_name')
                    ->label('Nama Bendahara')
                    ->view('display-or-dash'),
                TextEntry::make('activityWithTrashed.activity_type')
                    ->label('Tipe Kegiatan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SD' => 'danger',
                        'SMP' => 'primary',
                        default => 'info', // Untuk nilai lainnya atau jika kosong
                    })
                    ->icon('heroicon-o-academic-cap'),
                TextEntry::make('activityWithTrashed.activity_name')->label('Kegiatan'),
                TextEntry::make('activityWithTrashed.director_name')->label('Nama Direktur'),
                TextEntry::make('number_of_students')->label('Jumlah Siswa'),
                TextEntry::make('total_dpp')
                    ->label('Total DPP')
                    ->money('IDR')
                    ->state(function (CashProofOfExpenditure $record): float {
                        return ($record->activityWithTrashed?->dpp ?? 0) * $record->number_of_students;
                    }),
                TextEntry::make('total_ppn')
                    ->label('Total PPN')
                    ->money('IDR')
                    ->state(function (CashProofOfExpenditure $record): float {
                        return ($record->activityWithTrashed?->ppn ?? 0) * $record->number_of_students;
                    }),
                TextEntry::make('total_pph')
                    ->label('Total PPh')
                    ->money('IDR')
                    ->state(function (CashProofOfExpenditure $record): float {
                        return ($record->activityWithTrashed?->pph ?? 0) * $record->number_of_students;
                    }),
                TextEntry::make('nominal')->label('Total Nominal')->money('IDR'),
                TextEntry::make('sorted')->label('Terbilang'),
                TextEntry::make('deleted_at')
                    ->label('Status')
                    ->state(function (CashProofOfExpenditure $record): string {
                        return $record->deleted_at ? 'Tidak Aktif' : 'Aktif';
                    })
                    ->color(fn (string $state): string => $state === 'Aktif' ? 'success' : 'danger')
                    ->icon(fn (string $state): string => $state === 'Aktif' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->badge(),
            ]);
    }
    /**
     * Helper function untuk menghitung Nominal dan Terbilang secara otomatis.
     */
    public static function updateNominalAndSorted(Get $get, Set $set): void
    {
        $activityId = $get('activity_id');
        $numberOfStudents = (int) $get('number_of_students');

        if ($activityId && $numberOfStudents > 0) {
            $activity = Activity::withTrashed()->find($activityId); // <-- Pastikan withTrashed
            $activityTotal = $activity ? $activity->total : 0;
            $nominal = $activityTotal * $numberOfStudents;
            $set('nominal', $nominal);
            Config::set('terbilang.locale', 'id');
            $terbilangText = ucwords(Terbilang::make($nominal)) . ' Rupiah';
            $set('sorted', $terbilangText);
        } else {
            $set('nominal', 0);
            $set('sorted', '');
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // PERBAIKAN: Path relasi yang benar
                TextColumn::make('schoolWithTrashed.school_type')
                    ->label('Tingkat Sekolah')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SD' => 'danger',
                        'SMP' => 'primary',
                        default => 'info', // Untuk nilai lainnya atau jika kosong
                    })
                    ->icon('heroicon-o-academic-cap'),
                TextColumn::make('schoolWithTrashed.subdistrictWithTrashed.subdistrict_name')
                    ->label('Kecamatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('schoolWithTrashed.school_name')
                    ->label('Sekolah')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('activityWithTrashed.activity_name')
                    ->label('Kegiatan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('nominal')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label('Status')
                    ->state(fn (CashProofOfExpenditure $record): string => $record->deleted_at ? 'Tidak Aktif' : 'Aktif')
                    ->color(fn (string $state): string => $state === 'Aktif' ? 'success' : 'danger')
                    ->icon(fn (string $state): string => $state === 'Aktif' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d-M-Y')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('school')
                ->label('Tingkat Sekolah')
                ->relationship('school', 'school_type')
                ->searchable(),
                SelectFilter::make('school.subdistrict.subdistrict_name')
                ->label('Kecamatan')
                ->relationship('school.subdistrict', 'subdistrict_name')
                ->searchable(),
                SelectFilter::make('school')
                ->label('Sekolah')
                ->relationship('school', 'school_name')
                ->searchable(),
                SelectFilter::make('activity')
                ->label('Kegiatan')
                ->relationship('activity', 'activity_name')
                ->searchable(),
            ])
            ->actions([
                ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                Action::make('print')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (CashProofOfExpenditure $record): string => route('bkp.print', $record))
                    ->openUrlInNewTab(),
                DeleteAction::make()
                    ->label('Hapus')
                    ->requiresConfirmation()
                    ,
                Action::make('restore')
                    ->label('Pulihkan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (CashProofOfExpenditure $record) {
                        $record->restore();
                    })
                    ->hidden(fn (CashProofOfExpenditure $record) => $record->deleted_at === null),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashProofOfExpenditures::route('/'),
            'create' => Pages\CreateCashProofOfExpenditure::route('/create'),
            'view' => Pages\ViewCashProofOfExpenditure::route('/{record}'),
            'edit' => Pages\EditCashProofOfExpenditure::route('/{record}/edit'),
        ];
    }
}
