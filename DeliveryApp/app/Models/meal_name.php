<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class meal_name extends Pivot
{
    use HasFactory;
 
    protected $table = 'order_meal';

    // Additional fields or methods specific to the pivot table
}

