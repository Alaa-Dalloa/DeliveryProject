<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Add_ons extends Model
{
    use HasFactory;

  public function meal()
   {
    return $this->belongsTo(Meal::class);
   }

}
