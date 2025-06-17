<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subdistrict_id', // <-- TAMBAHKAN BARIS INI
        'school_type',
        'school_status',
        'school_name',
        'principal_name',
        'principal_nip',
        'treasurer_name',
        'treasurer_nip',
    ];

    public function subdistrict(): BelongsTo
    {
        return $this->belongsTo(Subdistrict::class, 'subdistrict_id');
    }

    public function cashProofOfExpenditures(): HasMany
    {
        return $this->hasMany(CashProofOfExpenditure::class);
    }
}
