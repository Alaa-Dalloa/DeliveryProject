<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bearen extends Model
{
    use HasFactory;
    protected $fillable = [
    'daily_sale_date',
    'total_sales',
    'owner_resturent_id'
];



  public function owner_resturent()
   {
    return $this->belongsTo(Owner_resturent::class);
   }

}
