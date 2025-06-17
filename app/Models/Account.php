<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
     protected $fillable = [
        'account',
        'payment_date',
        'user_id',
        'card_number',
        'password'
    ];

    public function account()
   {
    return $this->belongsTo(Account::class);
   }
 
}
