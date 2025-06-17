<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Owner_resturent;
use Validator;

class CategoryController extends Controller
{
 public function create(Request $request)
{
    $rules = array(
        "name" => "required",
        "photo" => "required",
    );
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return $validator->errors();
    } else {
        $category = new Category;
        $category->name = $request->name;
        if ($request->hasFile('photo')) {
            $photoName = rand() . time() . '.' . $request->photo->getClientOriginalExtension();
            $path = $request->file('photo')->move('upload/', $photoName);
            $category->photo = $photoName;
        }
        $category->save();
        $name = Auth::user()->name;
        $owner_resturent=Owner_resturent::where('owner_name', '=',$name)->first(); 
        $category->owner_resturents()->attach($owner_resturent);
        return response()->json($category, 201);
    
}}
 public function index()
 {
   return Category::all();
 }
public function show($id)
 { 
    return Category::find($id);
    
 }

 public function update ($id,Request $request)
 { 
  $rules=array(
    "name"=>"required|min:2|max:9"
  );
  $validator=Validator::make($request->all() , $rules);
  if($validator->fails()){
    return $validator->errors();
  }
  else
  {
   $category= Category::find($request->id);
   $category->name=request()->name;
   if($request->hasFile('photo'))
           {
            $photoName=rand().time().'.'.$request->photo->getClientOriginalExtension();
            $path=$request->file('photo')->move('upload/', $photoName );
            $category->photo=$photoName ;
           }
   
}
   $result=$category->save();
   if($result){
    return ["Result"=>"data has been updated"];
 }
 return ["Result"=>"operation failed"];
 }

public function delete($id)
{  
   $category= Category::find($id);
   $result=$category->delete();
   if($result)
   {
    return ["Result"=>"data has been deleted"];
 }
 return ["Result"=>"operation failed"];

}
public function searchOfCategory($id,$name)

    {
    $owner_resturent = Owner_resturent::find($id);
    return $owner_resturent->categories()->where("name","like","%".$name."%")->get();

    }


public function getCategoryMyOwner()
{
$name = Auth::user()->name;
$owner_restaurant=Owner_resturent::where('owner_name','=',$name)->first();
if ($owner_restaurant) {
return $owner_restaurant->categories()->get();
} else
{
return collect();
}
}

public function getCategoryByOwner($id)
{
    $owner_resturent = Owner_resturent::find($id);
    
    if ($owner_resturent) {
        return $owner_resturent->categories()->get();
    } else {
        return collect();
    }
}





}