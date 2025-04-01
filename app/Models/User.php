<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'dob',
        'address',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // A user (patient) can have many appointments
    public function appointments(): HasMany {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    // A user (doctor) can be assigned to many appointments
    public function doctorAppointments(): HasMany {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    // A user (patient) can have multiple medical records
    public function medicalRecords(): HasMany {
        return $this->hasMany(MedicalRecord::class, 'patient_id');
    }

    // A user (admin) uploads multiple medical records
    public function uploadedRecords(): HasMany {
        return $this->hasMany(MedicalRecord::class, 'uploaded_by');
    }
}
