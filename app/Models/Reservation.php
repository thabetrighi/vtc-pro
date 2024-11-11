<?php

namespace App\Models;

use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Reservation extends Model
{
    use HasFactory, SoftDeletes, AsSource, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mode',
        'pickup_location',
        'destination_location',
        'pickup_street',
        'pickup_zip_code',
        'pickup_city',
        'pickup_note',
        'destination_street',
        'destination_zip_code',
        'destination_city',
        'destination_note',
        'departure_at',
        'passenger_name',
        'passenger_email',
        'passenger_phone',
        'passenger_count',
        'additional_info',
        'alt_phone',
        'payment_method',
        'fare',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'mode'            => 'string',
        'departure_at'    => 'datetime',
        'passenger_count' => 'integer',
        'fare'            => 'decimal:2',
        'payment_method'  => 'string',
    ];

    /**
     * The attributes for which you can use filters in URL.
     *
     * @var array
     */
    protected $allowedFilters = [
        'mode'                   => Where::class,              // Assumes mode can be searched by a substring
        'pickup_location'        => Like::class,              // Similar treatment for pickup location
        'destination_location'   => Like::class,              // For destination location
        'pickup_street'          => Like::class,              // For pickup street address
        'pickup_zip_code'        => Like::class,              // For pickup zip code
        'pickup_city'            => Like::class,              // For pickup city
        'pickup_note'            => Like::class,              // For any notes associated with pickup
        'destination_street'     => Like::class,              // For destination street address
        'destination_zip_code'   => Like::class,              // For destination zip code
        'destination_city'       => Like::class,              // For destination city
        'destination_note'       => Like::class,              // For notes associated with destination
        'departure_at'           => WhereDateStartEnd::class, // For filtering based on departure date and time
        'passenger_name'         => Like::class,              // For passenger's name
        'passenger_email'        => Like::class,              // For passenger's email
        'passenger_phone'        => Like::class,              // For passenger's phone number
        'passenger_count'        => Where::class,             // Assuming this needs exact matching
        'additional_info'        => Like::class,              // For any additional information
        'alt_phone'              => Like::class,              // For alternative phone numbers
        'payment_method'         => Where::class,             // Exact match for payment method
        'fare'                   => Where::class,             // Exact match for fare
    ];

    /**
     * The attributes for which you can use sort in URL.
     *
     * @var array
     */
    protected $allowedSorts = [
        'mode',
        'pickup_location',
        'destination_location',
        'pickup_street',
        'pickup_zip_code',
        'pickup_city',
        'pickup_note',
        'destination_street',
        'destination_zip_code',
        'destination_city',
        'destination_note',
        'departure_at',
        'passenger_name',
        'passenger_email',
        'passenger_phone',
        'passenger_count',
        'additional_info',
        'alt_phone',
        'payment_method',
        'fare',
    ];

    /**
     * Get the users associated with the institution.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
