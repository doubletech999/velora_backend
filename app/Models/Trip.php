<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_name',
        'start_date',
        'end_date',
        'description',
        'sites',
        'price',
        'guide_name',
        'guide_id',
        'distance',
        'duration',
        'activities'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sites' => 'array',
        'activities' => 'array',
        'price' => 'decimal:2',
        'distance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->hasOne(Booking::class);
    }
}