<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\Order;
use App\Models\Star;
use App\Models\Meal;
use Carbon\Carbon;


class StarController extends Controller
{
public function addRatingToMeal($mealId)
{
    $validator = Validator::make(request()->all(), [
        'star_number' => 'required|min:1|max:5',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    $userId = auth()->user()->id;

    $order = Order::where('user_id', $userId)
        ->whereHas('meals', function ($query) use ($mealId) {
            $query->where('meal_id', $mealId);
        })
        ->first();

    if (!$order) {
        return response()->json('You are not authorized to rate this meal', 403);
    }

    $meal = Meal::find($mealId);

    $lastStar = \App\Models\Star::where('meal_id', $mealId)
        ->orderBy('created_at', 'desc')
        ->first();

    if ($lastStar) {
        
        $starNumber = ($lastStar->star_number + request('star_number')) / 2;
    } else {

        $starNumber = request('star_number')/2;
    }

    $star = \App\Models\Star::where('meal_id', $mealId)
        ->where('user_id', $userId)
        ->first();

    if ($star) {
       
        $star->star_number = $starNumber;
        $star->save();
    } else {
        // Create a new rating
        $star = new \App\Models\Star;
        $star->star_number = $starNumber;
        $star->meal_id = $mealId;
        $star->user_id = $userId;
        $star->save();
    }

    return response()->json('Rating added successfully', 200);
}


public function getLastRatingForMeal($mealId)
{
    $lastRating = Star::where('meal_id', $mealId)
        ->orderBy('created_at', 'desc')
        ->first();

    if ($lastRating) {
        $lastStarNumber = $lastRating->star_number;
        return $lastStarNumber;
    }

    return 0;

}



}
