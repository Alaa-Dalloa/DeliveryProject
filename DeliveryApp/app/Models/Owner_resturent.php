<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner_resturent  extends Model
{
    use HasFactory ;
    protected $fillable = [
        'owner_name',
        'resturent_name',
        'email',
        'password',
        'owner_phone',
        'resturent_phone',
        'blocked',
        'star_number'
    ];

    
    public function role()
   {
    return $this->belongsTo(Role::class);
   }

    public function meals()
   {
       return $this->hasMany(Meal::class);
   }
    public function bearens()
   {
       return $this->hasMany(Bearen::class);
   }
    public function bearen_m_s()
   {
       return $this->hasMany(BearenM::class);
   }
    public function bearen_years()
   {
       return $this->hasMany(BearenYear::class);
   }
   
   public function offers()
   {
       return $this->hasMany(Offer::class);
   }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'owner_category', 'owner_resturent_id', 'category_id');
    }
     public function advertisements()
   {
       return $this->hasMany(Advertisement::class);
   }

   public function orders()
    {
        return $this->belongsToMany(Order::class, 'owner_order', 'owner_resturent_id', 'order_id');
    }
}
