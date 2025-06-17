<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function meals()
   {
       return $this->hasMany(Meal::class);
   }

   public function owner_resturents()
    {
        return $this->belongsToMany(Owner_resturent::class, 'owner_category', 'category_id', 'owner_resturent_id');
    }

}
