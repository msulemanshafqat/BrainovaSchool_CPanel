<?php

namespace Modules\MainApp\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\MainApp\Database\factories\CityFactory;

class City extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): CityFactory
    {
        //return CityFactory::new();
    }

    public function country():BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
