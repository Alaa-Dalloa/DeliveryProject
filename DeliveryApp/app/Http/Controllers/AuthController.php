<?php
 
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use App\Models\Role;
use Validator;
use Illuminate\Http\Request;
use JWTAuth;
use Carbon\Carbon;
use DB;
use Tymon\JWTAuth\Exceptions\JWTException;
 
class AuthController extends Controller
{
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register() {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:5',
            'phone'=> 'required|min:10',
        ]);
 
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
 
        $user = new User;
        $user->name = request()->name;
        $user->email = request()->email;
        $user->phone = request()->phone;
        $user->password = bcrypt(request()->password);
        $user->save();
        $customerRole = Role::where('name', 'customer')->first();  
        $user->roles()->attach($customerRole);
        return response()->json($user, 201);
    }
        
 
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
 
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user=User::where('email','=',$request->email)->get();
        return [
            'token'=>$token,
            'user'=>$user
            ];
    }
 
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $user = auth()->user();
        $addresses = $user->addresses;
        return response()->json([
            'user' => $user
        ]);
    }
 
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
 
        return response()->json(['message' => 'Successfully logged out']);
    }
 
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

public function addAddress(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    if ($user->roles()->where('name', 'Restaurant_manager')->exists()) {
        // Check if the user already has an associated address
        if ($user->addresses()->count() > 0) {
            return response()->json(['error' => 'Restaurant managers can only have one address'], 400);
        }
    }

    $address = new Address;
    $address->y = $request->y;
    $address->x = $request->x;
    $address->address_name = $request->address_name;
    $address->save();

    $user->addresses()->attach($address);

    return response()->json(['message' => 'Address added successfully'], 200);
}

public function updateAddress(Request $request, $addressId)
{
    $user = Auth::user();

    $address = Address::find($addressId);

    if (!$address) {
        return response()->json(['error' => 'Address not found'], 404);
    }

    if (!$user->addresses->contains($addressId)) {
        return response()->json(['error' => 'You do not have permission to do this'], 403);
    }

    $address->y=$request->y;
    $address->x=$request->x;
    $address->address_name=$request->address_name;
    $address->save();

    return response()->json(['message' => 'Address updated successfully'], 200);
}
public function deleteAddress(Request $request,$addressId)
{
    $user =Auth::user();
    $address =Address::find($addressId);
    if (!$address) 
    {
        return response()->json(['error' => 'Address not found'], 404);
    }
     if (!$user->addresses->contains($addressId)) {
        return response()->json(['error' => 'You do not have permission to do this'], 403);
    }

    $address->delete();

    return response()->json(['message' => 'Address deleted successfully'], 200);
}

public function getUserAddresses()
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    $addresses = $user->addresses;

    return response()->json($addresses, 200);
}
public function getCustomers()
{
    $role = Role::where('name', 'Customer')->first();

    if ($role) {
        return $role->users()->get();
    } else {
        return collect();
    }
}


public function blockUser($userId)
{
    $user = User::find($userId);
     
    if ($user->blocked_until && $user->blocked_until > Carbon::now()->tz('Europe/London')->addHours(3)) {
        $remainingTime = Carbon::parse($user->blocked_until)->diffForHumans();
        return response()->json(['message' => 'User is already blocked until '.$remainingTime], 200);
    }

    $blockedUntil = Carbon::now()->tz('Europe/London')->addHours(3)->addDay();
    $user->blocked_until = $blockedUntil;
    $user->blocked=1;
    $user->save();

    return response()->json(['message' => 'User blocked successfully until '.$blockedUntil], 200);
}


public function getAddress($restaurantId)
{
    $restaurantAddress = DB::table('address_user')
        ->join('addresses', 'address_user.address_id', '=', 'addresses.id')
        ->where('address_user.user_id', $restaurantId)
        ->select('addresses.address_name', 'addresses.x', 'addresses.y')
        ->first();

    if ($restaurantAddress) {
        return [
            'address_name' => $restaurantAddress->address_name,
            'x' => $restaurantAddress->x,
            'y' => $restaurantAddress->y
        ];
    } else {
        return null;
    }
}

public function DetaileAddress(Request $request, $addressId)
{

    $address = Address::find($addressId);

    if (!$address) {
        return response()->json(['error' => 'Address not found'], 404);
    }

    return response()->json([$address], 200);


}

}