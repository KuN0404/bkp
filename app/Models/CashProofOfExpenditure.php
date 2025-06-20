<?php

namespace App\Models;

use App\Models\School;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Riskihajar\Terbilang\Facades\Terbilang;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute; // <-- PASTIKAN INI DI-IMPORT

class CashProofOfExpenditure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'activity_id',
        'number_of_students',
        'nominal',
        'sorted',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (CashProofOfExpenditure $expenditure) {
            if ($expenditure->isDirty('nominal')) {
                Config::set('terbilang.locale', 'id');
                $expenditure->sorted = ucwords(Terbilang::make($expenditure->nominal)) . ' Rupiah';
            }
        });
    }

    // --- AREA PENAMBAHAN ACCESSOR ---

    /**
     * Accessor untuk menghitung Total PPN secara dinamis.
     */
    protected function totalPpn(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->activity?->ppn ?? 0) * $this->number_of_students,
        );
    }

    /**
     * Accessor untuk menghitung Total PPH secara dinamis.
     */
    protected function totalPph(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->activity?->pph ?? 0) * $this->number_of_students,
        );
    }

    /**
     * Accessor untuk menghitung jumlah total pajak (PPN + PPH).
     */
    protected function totalPajak(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_ppn + $this->total_pph,
        );
    }


    // --- RELASI ---

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schoolWithTrashed(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id')->withTrashed();
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
        public function activityWithTrashed()
    {
        // Relasi ini akan mengambil data kecamatan bahkan yang sudah di-soft-delete
        return $this->belongsTo(Activity::class, 'activity_id')->withTrashed();
    }
}
