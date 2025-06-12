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
        'director_name',
    ];

    public function cashProofOfExpenditures(): HasMany
    {
        return $this->hasMany(CashProofOfExpenditure::class);
    }
}
