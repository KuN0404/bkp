<?php

namespace App\Imports;

use App\Models\Activity;
use App\Models\CashProofOfExpenditure;
use App\Models\School;
use App\Models\Subdistrict;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CashProofOfExpendituresImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Nilai yang diizinkan untuk tipe sekolah dan status sesuai migrasi Anda
        $allowedSchoolTypes = ['SD', 'SMP', 'SMA'];
        $allowedSchoolStatus = ['Negeri', 'Swasta'];

        foreach ($rows as $row) {
            // Validasi awal untuk memastikan kolom kunci tidak kosong
            if (empty($row['nama_sekolah']) || empty($row['nama_kegiatan']) || empty($row['jumlah_siswa'])) {
                Log::warning('Baris dilewati karena nama sekolah, kegiatan, atau jumlah siswa kosong.', $row->toArray());
                continue;
            }

            // --- LANGKAH 1: CARI ACTIVITY TERLEBIH DAHULU ---
            $activity = Activity::where('activity_name', $row['nama_kegiatan'])->first();

            if (!$activity) {
                Log::warning('Baris dilewati: Kegiatan tidak ditemukan di database.', [
                    'nama_kegiatan_dicari' => $row['nama_kegiatan']
                ]);
                continue;
            }

            // --- LANGKAH 2: PROSES RELASI LAINNYA ---
            $subdistrict = Subdistrict::firstOrCreate(
                ['subdistrict_name' => $row['nama_kecamatan']],
            );

            // --- VALIDASI TIPE & STATUS SEKOLAH SEBELUM INSERT ---
            $schoolTypeFromExcel = $row['tipe_sekolah'] ?? null;
            $validatedSchoolType = 'SD'; // Nilai default baru: SD

            if (in_array($schoolTypeFromExcel, $allowedSchoolTypes)) {
                $validatedSchoolType = $schoolTypeFromExcel;
            } elseif ($schoolTypeFromExcel !== null) {
                Log::warning('Tipe sekolah tidak valid di Excel, menggunakan default (SD).', ['tipe_tidak_valid' => $schoolTypeFromExcel]);
            }

            $schoolStatusFromExcel = $row['status_sekolah'] ?? null;
            $validatedSchoolStatus = 'Negeri'; // Nilai default baru: Negeri

            if (in_array($schoolStatusFromExcel, $allowedSchoolStatus)) {
                $validatedSchoolStatus = $schoolStatusFromExcel;
            } elseif ($schoolStatusFromExcel !== null) {
                Log::warning('Tipe status tidak valid di Excel, menggunakan default (Negeri).', ['status_tidak_valid' => $schoolStatusFromExcel]);
            }
            // ---------------------------------------------

            $school = School::firstOrCreate(
                ['school_name' => $row['nama_sekolah']],
                [
                    'subdistrict_id' => $subdistrict->id,
                    'school_type'    => $validatedSchoolType,       // Gunakan nilai yang sudah divalidasi
                    'school_status'  => $validatedSchoolStatus,    // Gunakan nilai yang sudah divalidasi
                    'principal_name' => $row['nama_kepala_sekolah'] ?? null,
                    'principal_nip'  => $row['nip_kepala_sekolah'] ?? null,
                    'treasurer_name' => $row['nama_bendahara'] ?? null,
                    'treasurer_nip'  => $row['nip_bendahara'] ?? null,
                ]
            );

            // --- LANGKAH 3: PERHITUNGAN OTOMATIS BERDASARKAN DATA ACTIVITY ---
            $hargaPerSiswa = $activity->total;
            $jumlahSiswa = intval($row['jumlah_siswa']);
            $totalNominal = $jumlahSiswa * $hargaPerSiswa;

            // --- LANGKAH 4: BUAT DATA BKP ---
            CashProofOfExpenditure::firstOrCreate(
                [
                    'school_id'   => $school->id,
                    'activity_id' => $activity->id,
                ],
                [
                    'number_of_students' => $jumlahSiswa,
                    'nominal'            => $totalNominal,
                ]
            );
        }
    }
}
