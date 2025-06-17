<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Owner_resturent;
use Illuminate\Http\Request;
use Validator;
use App\Models\Meal;
use App\Models\Add_ons;
use App\Models\Without;
use App\Models\Order;

class MealController extends Controller
{
    public function addMeal(Request $request) {
       $validator = Validator::make(request()->all(), [
        'name' => 'required',
        'photo' => 'required',
        'price' => 'required',
        'category_id'=> 'required',
        ]);
 
        if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
        }
 
        $meal = new Meal;
        $meal->name = request()->name;
        $meal->price = request()->price;
        $meal->description = request()->description;
        if($request->hasFile('photo'))
           {
            $photoName=rand().time().'.'.$request->photo->getClientOriginalExtension();
            $path=$request->file('photo')->move('upload/', $photoName );
            $meal->photo=$photoName ;
           }
        $owner_email=auth()->user()->email;
        $resturentId=Owner_resturent::where('email','=',$owner_email)->value('id');
        $meal->owner_resturent_id =$resturentId;
        $meal->category_id = request()->category_id;
        $meal->save();
        return response()->json(['message' => 'meal has been added successfully'], 201);
    }


    public function MealByResturent()
    {
        $usre_id =Auth::id();
        $email=User::where('id',$usre_id)->value('email');
        $restaurantId=Owner_resturent::where('email',$email)->value('id');
        return Meal::where('owner_resturent_id',$restaurantId)->get();
    }
 
public function MealByResturentById($id)
{
return Meal::where('owner_resturent_id','=',$id )->get();

}

public function getMealsByCategoryAndOwner($resturentId, $categoryId)
{
    $owner_resturent = Owner_resturent::find($resturentId);
    
    if ($owner_resturent) {
        return $owner_resturent->categories()
            ->where('categories.id', $categoryId)
            ->with('meals')
            ->get();
    } else {
        return collect();
    }
}

public function showDetailMeal($id)
{
    $meal = Meal::find($id);

    if ($meal) {
        return $meal;
    } else {
        return response()->json(['message' => 'The meal not found']);
    }
}

public function updateMeal(Request $request, $id)
{
    $meal = Meal::find($id);

    if ($meal) {
        if ($request->filled('name')) {
            $meal->name = $request->input('name');
        }

        if ($request->filled('price')) {
            $meal->price = $request->input('price');
        }

        if ($request->filled('category_id')) {
            $meal->category_id = $request->input('category_id');
        }

        if ($request->filled('description')) {
            $meal->description = $request->input('description');
        }

        if ($request->hasFile('photo')) {
            $photoName = rand() . time() . '.' . $request->photo->getClientOriginalExtension();
            $path = $request->file('photo')->move('upload/', $photoName);
            $meal->photo = $photoName;
        }

        $result = $meal->save();

        if ($result) {
            return ["Result" => "Data has been updated"];
        }

        return ["Result" => "Operation failed"];
    }

    return ["Result" => "Meal not found"];
}

public function delete($id)
{  
   $meal= Meal::find($id);
   $result=$meal->delete();
   if($result)
   {
    return ["Result"=>"data has been deleted"];
 }
 return ["Result"=>"operation failed"];

}


public function addNewAddonsToMeal(Request $request, $mealId)
{
    $meal = Meal::find($mealId);
    
    if (!$meal) {
        return response()->json(['message' => 'Meal not found.'], 404);
    }
    

    $addon = new Add_ons();
    $addon->addon = request()->addon;
    $addon->meal_id = $meal->id;
    $addon->save();
    
    
    return response()->json(['message' => 'New addons added successfully to the meal.'], 200);
}

public function addNewWitoutToMeal(Request $request, $mealId)
{
    $meal = Meal::find($mealId);
    
    if (!$meal) {
        return response()->json(['message' => 'Meal not found.'], 404);
    }
    
   
    $Witout = new Without();
    $Witout->without_name = request()->without_name;
    $Witout->meal_id = $meal->id;
    $Witout->save();
    

    
    return response()->json(['message' => 'New Witouts added successfully to the meal.'], 200);
}

public function searchOfMeal($restaurant_id, $category_id,$mealName)
{
    return Meal::select('id', 'name', 'photo', 'price', 'owner_resturent_id', 'category_id', 'price_after_discount', 'end_date','description')
        ->where('name', 'like', '%' . $mealName . '%')
        ->where('owner_resturent_id', $restaurant_id)
        ->where('category_id', $category_id)
        ->get();
}


}
