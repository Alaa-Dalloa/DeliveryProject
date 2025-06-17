<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BearenM extends Model
{
    use HasFactory;
    protected $fillable = [
    'monthly_sale_date',
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
