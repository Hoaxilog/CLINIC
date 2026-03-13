<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements HasName
{
    public function getRoleLabelAttribute()
    {
        // These match the IDs in your database (1=admin/dentist, 2=staff, 3=patient)
        return match($this->role) {
            1 => 'Admin/Dentist',
            2 => 'Staff',
            3 => 'Patient',
            default => 'Unknown',
        };
    }

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'google_id',
        'name',
        'email',
        'password',
        'profile_picture',
        'first_name',
        'last_name',
        'bio',
        'phone',
        'position',
        'contact',
        'role',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
        ];
    }

    public function getFilamentName(): string
    {
        return (string) (
            $this->name
            ?? $this->username
            ?? $this->email
            ?? 'User'
        );
    }
}
