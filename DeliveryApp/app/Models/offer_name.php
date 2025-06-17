<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class offer_name extends Pivot
{
    use HasFactory;
 
    protected $table = 'offer_meal';

    // Additional fields or methods specific to the pivot table
}

