<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'guide_id',
        'path_id',
        'site_id',
        'booking_date',
        'start_time',
        'end_time',
        'total_price',
        'number_of_participants',
        'payment_method',
        'status',
        'notes'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'total_price' => 'decimal:2',
        'number_of_participants' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}

