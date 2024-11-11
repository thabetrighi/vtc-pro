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

class Institution extends Model
{
    use HasFactory, SoftDeletes, AsSource, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'zip_code',
        'city',
        'number',
        'street_name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
        'id'          => Where::class,
        'name'        => Like::class,
        'zip_code'    => Like::class,
        'city'        => Like::class,
        'number'      => Like::class,
        'street_name' => Like::class,
        'updated_at'  => WhereDateStartEnd::class,
        'created_at'  => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'zip_code',
        'city',
        'number',
        'street_name',
        'updated_at',
        'created_at',
    ];

    /**
     * Get the users associated with the institution.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'institution_id');
    }
}
