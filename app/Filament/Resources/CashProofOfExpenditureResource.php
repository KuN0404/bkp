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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Riskihajar\Terbilang\Facades\Terbilang;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\CashProofOfExpenditureResource\Pages;

class CashProofOfExpenditureResource extends Resource
{
    protected static ?string $model = CashProofOfExpenditure::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'BKP';
    protected static ?string $modelLabel = 'Bukti Kas Pengeluaran';
    protected static ?string $pluralModelLabel = 'Daftar BKP';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('subdistrict_id')
                    ->label('Pilih Kecamatan')
                    ->options(Subdistrict::all()->pluck('subdistrict_name', 'id'))
                    ->live()
                    ->searchable()
                    ->afterStateUpdated(fn (Set $set) => $set('school_id', null))
                    ->dehydrated(false) // Field ini tidak disimpan ke database, hanya sebagai helper
                    ->required(),

                Select::make('school_id')
                    ->label('Sekolah')
                    ->options(function (Get $get): Collection {
                        // Hanya tampilkan sekolah berdasarkan kecamatan yang dipilih
                        if (!$get('subdistrict_id')) {
                            return collect();
                        }
                        return School::query()
                            ->where('subdistrict_id', $get('subdistrict_id'))
                            ->pluck('school_name', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->required()
                    ->disabled(fn (Get $get): bool => !$get('subdistrict_id')), // Nonaktif jika kecamatan belum dipilih

                Select::make('activity_type')
                    ->label('Pilih Tipe Kegiatan')
                    ->options(Activity::all()->pluck('activity_type', 'activity_type'))
                    ->live()
                    ->searchable()
                    ->afterStateUpdated(fn (Set $set) => $set('activity_id', null))
                    ->dehydrated(false) // Field ini tidak disimpan ke database, hanya sebagai helper
                    ->required(),

                Select::make('activity_id')
                    ->label('Nama Kegiatan')
                    ->options(function (Get $get): Collection {
                        // Hanya tampilkan sekolah berdasarkan kecamatan yang dipilih
                        if (!$get('activity_type')) {
                            return collect();
                        }
                        return Activity::query()
                            ->where('activity_type', $get('activity_type'))
                            ->pluck('activity_name', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->required()
                    ->disabled(fn (Get $get): bool => !$get('activity_type')) // Nonaktif jika kecamatan belum dipilih
                    ->afterStateUpdated(function ($state, Set $set) {
                        // Ambil data activity berdasarkan id yang dipilih
                        $activity = Activity::find($state);
                        // Isi field 'director_name' dengan data dari activity
                        // Jika activity tidak ditemukan (misal, pilihan dikosongkan), isi dengan null.
                        $set('director_name', $activity?->director_name);
                    })
                    ->required(),

                    TextInput::make('director_name') // <-- Gunakan nama simpel, bukan relasi
                    ->label('Nama Direktur')
                    ->readOnly() // <-- Benar, hanya untuk dibaca
                    ->dehydrated(false), // <-- Benar, tidak perlu disimpan ke database

                TextInput::make('number_of_students')
                    ->label('Jumlah Siswa')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updateNominalAndSorted($get, $set)),

                TextInput::make('nominal')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly() // <-- Dibuat read-only
                    ->required(),

                Textarea::make('sorted')
                    ->label('Terbilang')
                    ->readOnly() // <-- Dibuat read-only
                    ->required(),
            ]);
    }

    /**
     * Menyiapkan data sebelum form diisi (untuk halaman Edit dan View).
     * Ini akan memastikan dropdown Kecamatan dan Sekolah menampilkan data yang benar.
     */
    public static function mutateFormDataBeforeFill(array $data): array
    {
        $record = CashProofOfExpenditure::find($data['id']);
        if ($record) {
            // Mengisi dropdown kecamatan helper berdasarkan data sekolah yang ada
            $data['subdistrict_id'] = $record->school?->subdistrict_id;
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
                TextEntry::make('school.subdistrict.subdistrict_name')->label('Kecamatan'),
                TextEntry::make('school.school_name')->label('Sekolah'),
                ViewEntry::make('school.principal_name')
                ->label('Nama Kepala Sekolah')
                ->view('display-or-dash'),
                ViewEntry::make('school.treasurer_name')
                ->label('Nama Bendahara')
                ->view('display-or-dash'),
                TextEntry::make('activity.activity_type')->label('Tipe Kegiatan'),
                TextEntry::make('activity.activity_name')->label('Kegiatan'),
                TextEntry::make('activity.director_name')->label('Nama Direktur'),
                TextEntry::make('number_of_students')->label('Jumlah Siswa'),
                TextEntry::make('total_dpp')
                    ->label('Total DPP')
                    ->money('IDR')
                    ->state(function (CashProofOfExpenditure $record): float {
                        // Menghitung PPN: ppn per kegiatan * jumlah siswa
                        return ($record->activity?->dpp ?? 0) * $record->number_of_students;
                    }),
                TextEntry::make('total_ppn')
                    ->label('Total PPN')
                    ->money('IDR')
                    ->state(function (CashProofOfExpenditure $record): float {
                        // Menghitung PPN: ppn per kegiatan * jumlah siswa
                        return ($record->activity?->ppn ?? 0) * $record->number_of_students;
                    }),
                TextEntry::make('total_pph')
                    ->label('Total PPh')
                    ->money('IDR')
                    ->state(function (CashProofOfExpenditure $record): float {
                        // Menghitung PPH: pph per kegiatan * jumlah siswa
                        return ($record->activity?->pph ?? 0) * $record->number_of_students;
                    }),
                TextEntry::make('nominal')->label('Total Nominal')->money('IDR'),
                TextEntry::make('sorted')->label('Terbilang'),
            ]);
    }

    /**
     * Helper function untuk menghitung Nominal dan Terbilang secara otomatis.
     */
    public static function updateNominalAndSorted(Get $get, Set $set): void
    {
        $activityId = $get('activity_id');
        $numberOfStudents = (int) $get('number_of_students');

        // Hanya hitung jika kedua input sudah terisi
        if ($activityId && $numberOfStudents > 0) {
            $activity = Activity::find($activityId);
            $activityTotal = $activity ? $activity->total : 0;

            // Hitung nilai nominal
            $nominal = $activityTotal * $numberOfStudents;

            // Set field 'nominal'
            $set('nominal', $nominal);

            // Set field 'sorted' (terbilang)
            Config::set('terbilang.locale', 'id');
            $terbilangText = ucwords(Terbilang::make($nominal)) . ' Rupiah';
            $set('sorted', $terbilangText);
        } else {
            // Reset jika salah satu input kosong
            $set('nominal', 0);
            $set('sorted', '');
        }
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.school_name')->label('Sekolah')->searchable()->sortable(),
                TextColumn::make('school.subdistrict.subdistrict_name')->label('Kecamatan')->searchable()->sortable(),
                TextColumn::make('activity.activity_name')->label('Kegiatan')->searchable(),
                TextColumn::make('nominal')->money('IDR')->sortable(),
                TextColumn::make('created_at')->label('Tanggal Dibuat')->dateTime('d-M-Y')->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('school')->relationship('school', 'school_name'),
                SelectFilter::make('activity')->relationship('activity', 'activity_name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('print')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn (CashProofOfExpenditure $record): string => route('bkp.print', $record))
                    ->openUrlInNewTab(),
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
