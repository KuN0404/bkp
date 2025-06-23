<?php

namespace App\Filament\Resources\CashProofOfExpenditureResource\Pages;

use App\Filament\Resources\CashProofOfExpenditureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Imports\CashProofOfExpendituresImport;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Exports\CashProofOfExpendituresExport;

class ListCashProofOfExpenditures extends ListRecords
{
    protected static string $resource = CashProofOfExpenditureResource::class;

  protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importBKP')
                ->label('Import BKP')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->form([
                    FileUpload::make('attachment')
                        ->label('File Excel')
                        ->required()
                        ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->disk('local') // Simpan sementara di disk 'local'
                        ->directory('imports'), // Opsional, untuk merapikan penyimpanan
                ])
                ->action(function (array $data) {
                    try {
                        // Jalankan import
                        Excel::import(new CashProofOfExpendituresImport, $data['attachment']);

                        // Kirim notifikasi sukses
                        Notification::make()
                            ->title('Import Berhasil')
                            ->body('Data BKP telah berhasil diimpor.')
                            ->success()
                            ->send();

                    } catch (ValidationException $e) {
                        // Tangani error validasi dari maatwebsite/excel
                        $failures = $e->failures();
                        $errorMessages = [];
                        foreach ($failures as $failure) {
                             $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
                        }

                        Notification::make()
                            ->title('Import Gagal: Terdapat Error Validasi')
                            ->body(implode('<br>', $errorMessages))
                            ->danger()
                            ->persistent() // Agar notifikasi tidak hilang sendiri
                            ->send();

                    } catch (\Exception $e) {
                        // Tangani error umum lainnya
                        Notification::make()
                            ->title('Import Gagal')
                            ->body('Terjadi kesalahan yang tidak terduga: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        Actions\Action::make('exportBKP')
            ->label('Export BKP')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function () {
                $filename = 'data-bkp-' . now()->format('Y-m-d') . '.xlsx';
                return Excel::download(new CashProofOfExpendituresExport(), $filename);
            }),
            Actions\Action::make('downloadTemplate')
                ->label('Unduh Template')
                ->icon('heroicon-o-arrow-down-circle')
                ->color('gray')
                ->url(asset('templates/template_import_bkp_otomatis.xlsx'))
                ->openUrlInNewTab(),
            ];

    }
}
