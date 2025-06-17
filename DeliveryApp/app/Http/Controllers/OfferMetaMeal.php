<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Meal;
use App\Models\User;
use App\Models\Offer;
use App\Models\Owner_resturent;
use Carbon\Carbon;
class OfferMetaMeal extends Controller
{
public function addOfferToMeals(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'photo' => 'required',
        'end_date' => 'required',
        'discount' => 'required|string',
        'meal_ids' => 'required',
        'meal_ids.*' => 'exists:meals,id',
    ]);

    $name = $request->input('name');
    if($request->hasFile('photo'))
           {
            $photoName=rand().time().'.'.$request->photo->getClientOriginalExtension();
            $path=$request->file('photo')->move('upload/', $photoName );
            $photo=$photoName;
           }
    $end_date = $request->input('end_date');
    $discount = $request->input('discount');
    $owner_email = auth()->user()->email;
    $resturentId = Owner_resturent::where('email', '=', $owner_email)->value('id');
    $owner_resturent_id = $resturentId;

    $currentDate = Carbon::now()->tz('Europe/London')->addHours(3);

    // Convert the end date to a Carbon instance
    $endDateTime = Carbon::parse($end_date);

    if ($endDateTime <= $currentDate) {
        return response()->json("End date should be greater than the current this date", 422);
    }

    // Calculate the price after discount
    $mealIds = $request->input('meal_ids');
    $totalPrice = Meal::whereIn('id', $mealIds)->sum('price');
    $price_after_discount = $discount*(1/100)*$totalPrice;

    $offer = Offer::create([
        'name' => $name,
        'photo' => $photo,
        'end_date' => $endDateTime,
        'discount' => $discount,
        'price_after_discount' => $price_after_discount,
        'owner_resturent_id' => $owner_resturent_id,
    ]);

    foreach ($mealIds as $mealId) {
        $meal = Meal::find($mealId);

        if ($meal) {
            $offer->meals()->attach($meal);
        }
    }

    return response()->json("Offer added to meals successfully");
}

public function getPackageOffers($restaurantId)
{
    $currentDate = Carbon::now()->tz('Europe/London')->addHours(3);
    $offers = Offer::where('end_date', '<', $currentDate)->get();
    $packageOffers = Offer::select('id', 'name', 'photo', 'discount', 'end_date', 'price_after_discount', 'owner_resturent_id')
        ->where('owner_resturent_id', $restaurantId)
        ->whereDate('end_date', '>', $currentDate)
        ->get();

    foreach ($offers as $offer) {
        if ($offer->end_date < $currentDate) {
            
            $offer->orders()->detach();

            $offer->meals()->detach();

            $offer->delete();
        }
    }

    return response()->json($packageOffers);
}


public function detailOfferPackage($offerPackageId)
{
    $offer = Offer::find($offerPackageId);

    if ($offer) {
        if ($offer->end_date < Carbon::now()) {
            return response()->json(['message' => 'The offer has expired']);
        } else {
            return $offer->meals()->get();
        }
    } else {
        return response()->json(['message' => 'The offer has expired']);
    }
}


public function deletePackage($offerId)
{
    $offer = Offer::find($offerId);

    if (!$offer) {
        return response()->json("Offer not found", 404);
    }

    $meals = $offer->meals;

    if (!empty($meals)) {
        $offer->meals()->detach();
    }

    $offer->delete();

    return response()->json("Offer canceled and its meals have been removed successfully");
}


public function editOfferPackage(Request $request, $offerId)
{

    $name = $request->input('name');
    if($request->hasFile('photo'))
           {
            $photoName=rand().time().'.'.$request->photo->getClientOriginalExtension();
            $path=$request->file('photo')->move('upload/', $photoName );
            $photo=$photoName ;
           }
    $end_date = $request->input('end_date');
    $discount = $request->input('discount');

    $offer = Offer::find($offerId);

    if (!$offer) {
        return response()->json("Offer not found", 404);
    }

    $currentDate = Carbon::now()->tz('Europe/London')->addHours(3);

    if ($end_date <= $currentDate) {
        return response()->json("End date should be greater than the current date", 422);
    }

    // Calculate the price after discount
    $mealIds = $request->input('meal_ids');
    $totalPrice = Meal::whereIn('id', $mealIds)->sum('price');
    $price_after_discount = $discount*(1/100)*$totalPrice;

    $offer->name = $name;
    $offer->photo = $photo;
    $offer->end_date = $end_date;
    $offer->discount = $discount;
    $offer->price_after_discount = $price_after_discount;
     $offer->meals()->detach();

	foreach ($mealIds as $mealId) {
	    $meal = Meal::find($mealId);

	    if ($meal) {
	        $offer->meals()->attach($mealId);
	    }
	}
	    return response()->json("Offer updated to meals successfully");
}


}