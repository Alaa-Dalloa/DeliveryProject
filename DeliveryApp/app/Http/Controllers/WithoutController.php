<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Without;

class WithoutController extends Controller
{
   public function getWithoutDetails($addon_id)
{
    $Without = Without::find($addon_id);

    return response()->json([$Without
    ]);
} 

public function showDetailWithoutMeal($meal_id)
{
    $Without = Without::where('meal_id', $meal_id)->get();

    return response()->json($Without);
} 

}
