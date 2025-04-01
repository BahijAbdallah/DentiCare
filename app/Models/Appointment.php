<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'status',
        'patient_id',
        'doctor_id'
    ];

    // An appointment belongs to one user (patient) only
    public function patient(): BelongsTo {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // An appointment is assigned to one doctor only
    public function doctor(): BelongsTo {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
