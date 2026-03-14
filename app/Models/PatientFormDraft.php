<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientFormDraft extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
        'mode',
        'step',
        'payload_json',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}

