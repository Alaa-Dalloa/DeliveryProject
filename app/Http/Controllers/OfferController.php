<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\User;
use App\Services\SendNotificationsService;

use Carbon\Carbon;
class OfferController extends Controller
{
public function addOfferToMeal($mealId,Request $request)
{
    $meal = Meal::find($mealId);
    $price_after_discount = $request->input('price_after_discount');
    $end_date = $request->input('end_date');

    if (!$meal) {
        return response()->json("Meal not found", 404);
    }
    $currentDate = Carbon::now()->tz('Europe/London')->addHours(3);
     if ($end_date <= $currentDate) {
        return response()->json("End date should be greater than the current date", 422);
    }
     if ($price_after_discount >= $meal->price) {
        return response()->json("New price should be less than the current price of the meal", 422);
    }
    $meal->price_after_discount = $price_after_discount;
    $meal->end_date = $end_date;
    $meal->save();
    
     $users=User::whereHas('roles', function ($query) {
        $query->where('name', 'Customer');
         })->get();

    foreach ($users as $user) {
        $message = [
            'title' =>'There are new offers',
            'body' =>$meal->name,
        ];

        (new SendNotificationsService)->sendByFcm($user->fcm_token,$message);
    }

    return response()->json("Offer added to the meal successfully");
}

public function getOffers($restaurantId)
{
    $currentDate = Carbon::now()->tz('Europe/London')->addHours(3);
    $meals = Meal::where('end_date', '<', $currentDate)->get();
    $offers = Meal::select('meals.id', 'meals.name', 'meals.photo', 'meals.price', 'meals.price_after_discount', 'meals.end_date','meals.description','stars.star_number')
        ->where('meals.owner_resturent_id', $restaurantId)
        ->whereNotNull('meals.end_date')
        ->where('meals.end_date', '>', $currentDate)
        ->leftJoin('stars', 'meals.id', '=', 'stars.meal_id')
        ->get();

    foreach ($meals as $meal) {
        if ($meal->end_date < $currentDate) {
            $meal->end_date = null;
            $meal->price_after_discount = null;
            $meal->save();
        }
    }

    return response()->json($offers);
}

public function deleteOffer($mealId)
{
    $meal = Meal::find($mealId);
    $meal->end_date = 'null';
    $meal->price_after_discount = 'null';
    $meal->save();
    return response()->json("offer has been deleted successfully");
}


public function editOffer($mealId,Request $request)
{
    $meal = Meal::find($mealId);
    $price_after_discount = $request->input('price_after_discount');
    $end_date = $request->input('end_date');

    if (!$meal) {
        return response()->json("Meal not found", 404);
    }
    $currentDate = Carbon::now()->tz('Europe/London')->addHours(3);
     if ($end_date <= $currentDate) {
        return response()->json("End date should be greater than the current date", 422);
    }
     if ($price_after_discount >= $meal->price) {
        return response()->json("New price should be less than the current price of the meal", 422);
    }
    $meal->price_after_discount = $price_after_discount;
    $meal->end_date = $end_date;
    $meal->save();

    return response()->json("Offer updated to the meal successfully");
}





}
