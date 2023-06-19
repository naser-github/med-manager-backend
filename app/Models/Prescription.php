<?php

namespace App\Models;

use App\Models\Medicine;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Prescription extends Model
{
    use HasFactory;

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'fk_medicine_id');
    }

    public function dose(): MorphMany
    {
        return $this->morphMany('App\Models\Dosage', 'dosage');
    }
}
