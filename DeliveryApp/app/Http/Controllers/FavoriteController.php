<?php 

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Meal;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function addToFavorites(Request $request ,$id)
    {
        $user = Auth::user();
        $meal = Meal::find($id);

        if ($user && $meal) {
            $favorite = new Favorite;
           $favorite->user_id =$user->id;
           $favorite->meal_id = $id;
           $favorite=$favorite->save();
            return response()->json(['message' => 'ok']);
        } else {
            return response()->json(['message' => 'failed'], 404);
        }
    }

    public function removeFromFavorites(Request $request, $id)
{
    $user = Auth::user();
    $meal = Meal::find($id);

    if ($user && $meal) {
        $favorite = Favorite::where('user_id', $user->id)
            ->where('meal_id', $id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'ok']);
        } else {
            return response()->json(['message' => 'Not found'], 404);
        }
    } else {
        return response()->json(['message' => 'Failed'], 404);
    }
}

public function getFavorites(Request $request)
{
    $user = Auth::user();

    if ($user) {
        $favorites = Favorite::where('user_id', $user->id)->get();
        $mealIds = $favorites->pluck('meal_id');
        $meals = Meal::whereIn('id', $mealIds)->get();

        return response()->json(['meals' => $meals]);
    } else {
        return response()->json(['message' => 'يجب تسجيل الدخول لعرض قائمة المفضلة.'], 401);
    }
}

}

