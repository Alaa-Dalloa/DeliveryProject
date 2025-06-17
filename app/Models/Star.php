<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Star extends Model
{
    use HasFactory;
     protected $table = 'stars';
    protected $fillable = [
        'star_number'
    ];

     public function user()
   {
    return $this->belongsTo(User::class);
   }
   public function meal()
   {
    return $this->belongsTo(Meal::class);
   }
}
