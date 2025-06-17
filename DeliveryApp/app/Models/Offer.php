<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
   protected $fillable = [
    'name',
    'photo',
    'end_date',
    'discount',
    'price_after_discount',
    'owner_resturent_id'
   ];

   public function meals()
    {
        return $this->belongsToMany(Meal::class, 'offer_meal', 'offer_id', 'meal_id');
    }


    public function owner_resturent()
   {
    return $this->belongsTo(Owner_resturent::class);
   }

   
    public function advertisements()
   {
       return $this->hasMany(Advertisement::class);
   }

   public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_offer', 'offer_id', 'order_id');
    }
}
