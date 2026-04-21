<?php

namespace Modules\BehaviourRecord\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BehaviourRecord\Database\factories\IncidentFactory;

class Incident extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'title',
        'description',
        'points',

    ];

    protected static function newFactory(): IncidentFactory
    {
        //return IncidentFactory::new();
    }
}
