<?php

namespace App\Filament\Resources;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\School; // <-- Import model School
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use App\Models\CashProofOfExpenditure;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action; // <-- Import Action
use Illuminate\Support\Facades\Config; // <-- Import Config
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder; // <-- Import Builder
use Filament\Forms\Get; // <-- Import Get untuk dependency
use Filament\Tables\Filters\TrashedFilter;
use Riskihajar\Terbilang\Facades\Terbilang;
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
                Select::make('subdistrict_id_helper')
                    ->label('Kecamatan')
                    ->relationship('school.subdistrict', 'subdistrict_name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('school_id', null)),

                Select::make('school_id')
                    ->label('Sekolah')
                    ->options(fn (Get $get): array =>
                        School::where('subdistric_id', $get('subdistrict_id_helper'))
                                ->pluck('school_name', 'id')->all()
                    )
                    ->searchable()
                    ->required()
                    ->disabled(fn (Get $get): bool => ! $get('subdistrict_id_helper')),

                Select::make('activity_id')
                    ->relationship('activity', 'activity_name')
                    ->label('Kegiatan')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('nominal')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (is_numeric($state)) {
                            Config::set('terbilang.locale', 'id');
                            $terbilangText = ucwords(Terbilang::make($state)) . ' Rupiah';
                            $set('sorted', $terbilangText);
                        } else {
                            $set('sorted', '');
                        }
                    }),

                Textarea::make('sorted')
                    ->label('Terbilang')
                    ->disabled()
                    ->required(),
            ]);
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
