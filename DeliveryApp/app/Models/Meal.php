<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    protected $fillable = [
    'name',
    'photo',
    'price',
    'description',
    'owner_resturent_id',
    'category_id',
    ];
   
    public function category()
   {
    return $this->belongsTo(Category::class);
   }

    public function owner_resturent()
   {
    return $this->belongsTo(Owner_resturent::class);
   }

   public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_meal', 'meal_id', 'offer_id');
    }

   public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_meal', 'meal_id', 'order_id')->withPivot('quantity','size', 'addons','withouts');
    }

    public function add_ons()
   {
       return $this->hasMany(Add_ons::class);
   }
   public function favorites()
{
    return $this->hasMany(Meal::class);
}

   public function withouts()
   {
       return $this->hasMany(Withouts::class);
   }

     public function comment()
   {
       return $this->hasMany(Comment::class);
   }

    public function stars()
   {
       return $this->hasMany(Star::class);
   }

    public function advertisements()
   {
       return $this->hasMany(Advertisement::class);
   }

}
