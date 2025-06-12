<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Riskihajar\Terbilang\Facades\Terbilang; // <-- Pastikan ini di-import

class CashProofOfExpenditure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'activity_id',
        'nominal',
        'sorted', // 'terbilang'
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
        ];
    }

    /**
     * Metode ini akan dijalankan secara otomatis oleh Laravel.
     * Kita akan menggunakannya untuk mengisi kolom 'sorted'.
     */
    protected static function booted(): void
    {
        // Event 'saving' akan dijalankan setiap kali model akan disimpan (create atau update)
        static::saving(function (CashProofOfExpenditure $expenditure) {
            // Cek jika nilai 'nominal' ada atau jika 'sorted' masih kosong
            if ($expenditure->nominal && (empty($expenditure->sorted) || $expenditure->isDirty('nominal'))) {
                // Buat nilai terbilang secara otomatis di sisi server
                $expenditure->sorted = ucwords(Terbilang::make($expenditure->nominal)) . ' Rupiah';
            }
        });
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
