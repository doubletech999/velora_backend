<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'languages',
        'phone',
        'hourly_rate',
        'is_approved',
        'specializations',      
        'certifications',       
        'experience_years',     
        'rating',               
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'experience_years' => 'integer',
        'rating' => 'decimal:1', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}