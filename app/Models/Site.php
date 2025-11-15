<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        // Basic fields
        'name',
        'name_ar',
        'description',
        'description_ar',
        'location',
        'location_ar',
        'latitude',
        'longitude',
        'type',
        'image_url',
        'images',
        
        // Route/Camping specific
        'length',
        'distance',
        'estimated_duration',
        'duration',
        'difficulty',
        'activities',
        'price',
        'guide_id',
        'guide_name',
        'warnings',
        'warnings_ar',
        'coordinates',
        
        // Hotel specific
        'star_rating',
        'price_per_night',
        'hotel_amenities',
        'hotel_amenities_ar',
        'room_count',
        'check_in_time',
        'check_out_time',
        
        // Restaurant specific
        'cuisine_type',
        'cuisine_type_ar',
        'average_price',
        'price_range',
        'opening_hours',
        'opening_hours_ar',
        'menu_url',
        
        // Tourist site specific
        'historical_period',
        'historical_period_ar',
        'entrance_fee',
        'best_time_to_visit',
        'best_time_to_visit_ar',
        
        // Camping specific
        'camping_amenities',
        'camping_amenities_ar',
        'capacity',
        
        // Contact fields
        'contact_phone',
        'contact_email',
        'address',
        'city',
        'website',
        'working_hours',
        
        // Common fields
        'rating',
        'review_count',
    ];

    protected $casts = [
        'images' => 'array',
        'activities' => 'array',
        'warnings' => 'array',
        'warnings_ar' => 'array',
        'coordinates' => 'array',
        'hotel_amenities' => 'array',
        'hotel_amenities_ar' => 'array',
        'camping_amenities' => 'array',
        'camping_amenities_ar' => 'array',
        'opening_hours' => 'array',
        'opening_hours_ar' => 'array',
        'working_hours' => 'array',
        'price' => 'decimal:2',
        'price_per_night' => 'decimal:2',
        'average_price' => 'decimal:2',
        'entrance_fee' => 'decimal:2',
        'length' => 'decimal:2',
        'distance' => 'decimal:2',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
        'estimated_duration' => 'integer',
        'room_count' => 'integer',
        'capacity' => 'integer',
        'star_rating' => 'integer',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }
}
