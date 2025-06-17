<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
     public function meal()
   {
    return $this->belongsTo(Meal::class);
   }
    public function user()
   {
    return $this->belongsTo(User::class);
   }
}
