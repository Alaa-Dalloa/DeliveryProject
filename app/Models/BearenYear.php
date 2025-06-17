<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BearenYear extends Model
{
    use HasFactory;
    protected $fillable = [
    'annual_sale_date',
    'total_sales',
    'total_delivery_cost',
    'total_summation',
    'owner_resturent_id'
];



  public function owner_resturent()
   {
    return $this->belongsTo(Owner_resturent::class);
   }
}
