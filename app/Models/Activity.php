<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    // Laravel akan mengasumsikan nama tabel adalah 'activities'.
    // Jika Anda tetap menggunakan 'activitys', tambahkan baris ini:
    // protected $table = 'activitys';

    protected $fillable = [
        'activity_name',
        'dpp',
        'ppn',
        'pph',
        'total',
        'director_name',
    ];

    protected function casts(): array // <-- TAMBAHKAN METHOD INI
    {
        return [
            'dpp' => 'decimal:2',
            'ppn' => 'decimal:2',
            'pph' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

        /**
     * The "booted" method of the model.
     * Ini adalah best practice untuk memastikan integritas data.
     */
    protected static function booted(): void
    {
        // Event 'saving' akan dijalankan setiap kali model akan disimpan (create atau update)
        static::saving(function (Activity $activity) {
            // Hitung ulang total dari dpp, ppn, dan pph untuk memastikan nilainya selalu benar.
            $activity->total = ($activity->dpp ?? 0) + ($activity->ppn ?? 0) + ($activity->pph ?? 0);
        });
    }
    public function cashProofOfExpenditures(): HasMany
    {
        return $this->hasMany(CashProofOfExpenditure::class);
    }
}
