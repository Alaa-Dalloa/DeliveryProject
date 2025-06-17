<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'x',
        'y',
        'address_name'
    ];
    

    public function users()
 {
    return $this->belongsToMany(User::class, 'address_user', 'address_id', 'user_id');
 }
 
  public function orders()
   {
       return $this->hasMany(Order::class);
   }
    
 

}
