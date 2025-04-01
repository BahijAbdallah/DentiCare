<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'url',
        'patient_id',
        'uploaded_by'
    ];

    // A medical record belongs to one user (patient) only
    public function patient(): BelongsTo {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // a medical record is uploaded by one admin only
    public function uploadedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
