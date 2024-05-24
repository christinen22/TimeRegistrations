<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
        'personnummer', // Added for personnummer field
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email', // Added to hide email
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
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'personnummer'; // Set to use personnummer as the identifier
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->personnummer; // Return personnummer as the identifier
    }

    /**
     * Override the default method to specify personnummer as the username.
     *
     * @return string
     */
    public function username()
    {
        return 'personnummer';
    }

    /**
     * Define a relationship with TimeStamp model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timeStamps()
    {
        return $this->hasMany(TimeStamp::class);
    }
}
