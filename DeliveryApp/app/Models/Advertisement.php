<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'photo',
        'type',
        'meal_id',
        'offer_id',
        'Agree',
        'owner_resturent_id'
    ];

    public function owner_resturent()
   {
    return $this->belongsTo(Owner_resturent::class);
   }

    public function offer()
   {
    return $this->belongsTo(Offer::class);
   }

      public function meal()
   {
    return $this->belongsTo(Meal::class);
   }
}
