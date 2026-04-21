<?php

namespace Modules\MainApp\Entities;

use App\Models\Tenant;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class School extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function scopeActive($query)
    {
        return $query->where('status', \App\Enums\Status::ACTIVE);
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'sub_domain_key', 'id');
    }

     public function city()
        {
            return $this->belongsTo(City::class, 'city_id', 'id');
        }

        public function country(): HasOneThrough
        {
            return $this->hasOneThrough(
                Country::class,  // Final model
                City::class,     // Intermediate model
                'id',            // Foreign key on cities table (city.id = city_id in schools)
                'id',            // Foreign key on countries table (country.id = country_id in cities)
                'city_id',       // Local key on schools table
                'country_id'     // Local key on cities table
            );
        }


         public function logo(): BelongsTo
            {
                return $this->belongsTo(Upload::class, 'logo_id', 'id');
            }
}
