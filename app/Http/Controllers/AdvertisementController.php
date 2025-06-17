<?php

namespace App\Http\Controllers;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use App\Models\User;
use App\Models\Owner_resturent;
use Illuminate\Support\Facades\Auth;
use App\Services\SendNotificationsService;

class AdvertisementController extends Controller
{
public function addAdvertisement($id ,Request $request)
{
    $validator = Validator::make(request()->all(),[
        'name' => 'required',
        'photo' => 'required',
        'type' => 'required|in:meal,offer',
        ]);
 
        if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
        }
    $advertisement = new Advertisement;
    $advertisement->name = request()->name;
    if($request->hasFile('photo'))
           {
            $photoName=rand().time().'.'.$request->photo->getClientOriginalExtension();
            $path=$request->file('photo')->move('upload/', $photoName );
            $advertisement->photo=$photoName ;
           }
    $advertisement->type = request()->type;
    $owner_email = auth()->user()->email;
    $resturentId = Owner_resturent::where('email', '=', $owner_email)->value('id');
    $advertisement->owner_resturent_id = $resturentId;

    if ($request->type === 'meal') {
        $advertisement->meal_id = $id;
    } elseif ($request->type === 'offer') {
        $advertisement->offer_id = $id;
    }

    $advertisement->save();

    $fcmToken=User::whereHas('roles', function ($query) {
        $query->where('name', 'super_admin');
    })->value('fcm_token');
    $message=[
        'title'=>'there are new advertisement',
        'body'=>$advertisement->name,
    ];

    (new SendNotificationsService)->sendByFcm($fcmToken,$message);
    

    return response()->json("Advertisement added successfully");
}

public function showAdvertisements()
{
    $dateLimit = Carbon::now()->tz('Europe/London')->addHours(2)->subDay();

    $advertisements = Advertisement::where(function ($query) {
        $query->whereNotNull('meal_id')
            ->orWhereNotNull('offer_id');
    })
        ->where('created_at', '>=', $dateLimit)
        ->where('Agree', 0) 
        ->take(5)
        ->get();

    $advertisementIds = $advertisements->pluck('id')->toArray();

    foreach ($advertisements as $advertisement) {
        $expirationDate = $advertisement->created_at->addDay();

        if (Carbon::now()->tz('Europe/London')->addHours(2)->greaterThan($expirationDate)) {
            $advertisement->delete();
        }
    }

    $newAdvertisements = Advertisement::where(function ($query) use ($advertisementIds) {
        $query->whereNotNull('meal_id')
            ->orWhereNotNull('offer_id');
    })
        ->where('created_at', '>=', $dateLimit)
        ->whereNotIn('id', $advertisementIds)
        ->where('Agree', 0) 
        ->take(5 - $advertisements->count())
        ->get();

    $allAdvertisements = $advertisements->concat($newAdvertisements);

    // Transform the data to include restaurant names
    $transformedAdvertisements = $allAdvertisements->map(function ($advertisement) {
        $restaurantName = null; // Initialize restaurant name

        // If the advertisement has an owner_resturent_id, get the restaurant name
        if ($advertisement->owner_resturent_id) {
            $restaurant = Owner_resturent::find($advertisement->owner_resturent_id);
            if ($restaurant) {
                $restaurantName = $restaurant->resturent_name;
            }
        }

        return [
            'id' => $advertisement->id,
            'name'=>$advertisement->name,
            'photo'=>$advertisement->photo,
            'type'=>$advertisement->type,
            'Agree'=>$advertisement->Agree,
            'meal_id' => $advertisement->meal_id,
            'offer_id' => $advertisement->offer_id,
            'restaurant_name' => $restaurantName, 
            'created_at' => $advertisement->created_at
        ];
    });

    return response()->json([
        'advertisements' => $transformedAdvertisements
    ]);
}
 public function AgreeAdvertisement($AdvertisementId)
    {
        $advertisement = Advertisement::find($AdvertisementId);
        if ($advertisement->Agree == '0') {

            return response()->json(['message' => 'The advertisement is pre-approved'], 200);
        }
        $advertisement->Agree = '0';
        $advertisement->save();
        $name=$advertisement->name;
        
        $users=User::whereHas('roles', function ($query) {
        $query->where('name', 'Customer');
         })->get();

    foreach ($users as $user) {
        $message = [
            'title' =>'There are new Advertisements',
            'body' =>Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d'),
        ];

        (new SendNotificationsService)->sendByFcm($user->fcm_token,$message);
    }
    
        $owner_resturent_id = Advertisement::where('id', $AdvertisementId)->value('owner_resturent_id');
        $email = Owner_resturent::where('id', $owner_resturent_id)->value('email');
        $fcmToken = User::where('email', $email)->value('fcm_token');
        
        $message = [
            'title' =>'Advertisement approved',
            'body' =>Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d'),
        ];
        
        (new SendNotificationsService)->sendByFcm($fcmToken, $message);
        
        
    return response()->json(['message' => 'The advertisement has been approved'], 200);
    }

public function Decline_to_advertise($AdvertisementId)
{
    $advertisement = Advertisement::find($AdvertisementId);

    if ($advertisement) {
        $owner_resturent_id = $advertisement->owner_resturent_id;
        $email = Owner_resturent::where('id', $owner_resturent_id)->value('email');
        $fcmToken = User::where('email', $email)->value('fcm_token');

        $message = [
            'title' => 'The Advertisement was rejected',
            'body' => Carbon::now()->tz('Europe/London')->addHours(2)->format('Y-m-d'),
        ];

        (new SendNotificationsService)->sendByFcm($fcmToken, $message);

        $advertisement->delete();

        return response()->json(['message' => 'The advertisement was successfully deleted'], 200);
    } else {
        return response()->json(['error' => 'Advertisement not found'], 404);
    }
}


  public function allAdvertisements()
 {
    $allAdvertisements=Advertisement::where('Agree',1)->get();
    return response()->json([$allAdvertisements], 200);
 }


}
