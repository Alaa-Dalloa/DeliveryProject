<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Add_ons;

class AddOnsController extends Controller
{
   public function getAddonsDetails($addon_id)
{
    $addon = Add_ons::find($addon_id);

    return response()->json([$addon
    ]);
} 

public function showDetailAddionMeal($meal_id)
{
    $addons = Add_ons::where('meal_id', $meal_id)->get();

    return response()->json($addons);
}

}
