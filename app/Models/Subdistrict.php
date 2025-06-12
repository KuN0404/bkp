<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subdistrict extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subdistrict_name',
    ];

    public function schools(): HasMany
    {
        return $this->hasMany(School::class);
    }
}
