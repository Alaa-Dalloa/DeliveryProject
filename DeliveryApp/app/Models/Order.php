<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable=[
    'status',
    'order_price',
    'delivery_cost',
    'user_id',
    'order_date',
    'address_id'
    ];

    public function user()
   {
    return $this->belongsTo(User::class);
   }

   public function meals()
    {
        return $this->belongsToMany(Meal::class, 'order_meal', 'order_id', 'meal_id')->withPivot('quantity','size', 'addons','withouts');
    }
  
   public function owner_resturents()
    {
        return $this->belongsToMany(Owner_resturent::class, 'owner_order', 'order_id', 'owner_resturent_id');
    }

     public function offers()
    {
        return $this->belongsToMany(Offer::class, 'order_offer', 'order_id', 'offer_id');
    }
   
   public function address()
   {
    return $this->belongsTo(Address::class);
   }
}
