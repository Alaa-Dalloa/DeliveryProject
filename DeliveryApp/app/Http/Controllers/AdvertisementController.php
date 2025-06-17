<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Owner_resturent;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

class AdvertisementController extends Controller
{
public function addAdvertisement($id ,Request $request)
{
    $validator = Validator::make(request()->all(), [
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

    return response()->json("Advertisement added successfully");
}

public function showAdvertisements()
{
    $dateLimit = Carbon::now()->tz('Europe/London')->addHours(3)->subDay();

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

        if (Carbon::now()->tz('Europe/London')->addHours(3)->greaterThan($expirationDate)) {
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

    return response()->json([
        'advertisements' => $allAdvertisements
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

        return response()->json(['message' => 'The advertisement has been approved'], 200);
    }

public function Decline_to_advertise($AdvertisementId)
{
    $advertisement = Advertisement::find($AdvertisementId)->delete();

    return response()->json(['message' => 'The advertisement was successfully deleted'], 200);
}

  public function allAdvertisements()
 {
    $allAdvertisements=Advertisement::where('Agree',1)->get();
        return response()->json([$allAdvertisements], 200);
 }


}
