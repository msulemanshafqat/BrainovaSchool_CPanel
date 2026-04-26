<?php

namespace Modules\MainApp\Entities;

use Modules\MainApp\Entities\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\MainApp\Database\factories\CountryFactory;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected static function newFactory(): CountryFactory
    {
        //return CountryFactory::new();
    }

    public function cities()
    {
        return $this->hasMany(City::class)->where('status', 1);
    }
}
