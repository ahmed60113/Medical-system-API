<?php

namespace MEDICAL\Countries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use MongoDB\Operation\Count;

class Country extends Model
{
    use HasFactory , SoftDeletes;
    
    protected $fillable = [
        'name', 'phone_code', 'parent_id'
    ];

        /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
   

       protected $dates = [
        'deleted_at', 'updated_at', 'created_at'
    ];

    protected $casts = [
        'name' => 'array'
    ];

    public function parent()
    {
        return $this->belongsTo(Country::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Country::class, 'parent_id')->with('children');
    }



}
