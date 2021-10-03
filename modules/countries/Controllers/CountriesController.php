<?php

namespace MEDICAL\Countries\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use MEDICAL\Countries\Models\Country;
use Laravel\Lumen\Routing\Controller as BaseController;
use MEDICAL\Countries\Resources\CountryResource;
use MongoDB\Operation\Count;

class CountriesController extends BaseController
{

   public static function setCache($data, $key, $exp)
   {
      $cached = app('redis')->set($key, $data);
      //app('redis')->expire($key,$exp);
      return $cached;
   }

   public static function getCache($key)
   {
      if ($key) {
         return json_decode(app('redis')->get($key),true);
      } else
         abort(404, 'Cahce not found');
   }

   public static function createList()
   {
      $countries = Country::with('children')->select('id', DB::raw('json_extract(name,"$.ar") as arabicName , json_extract(name,"$.en") as englishName'))
         ->wherenull('parent_id')->get();

      $countries1 = CountryResource::collection($countries);
      $encode = json_encode($countries1);
      return self::setCache($encode, 'Areas', 30 * 24 * 60 * 60);
   }

   public static function create(Request $request)
   {

      $validate = \Validator::make($request->all(), [
         'arName' => 'required',
         'enName' => 'required',
         'phone_code' => 'nullable|unique:countries,phone_code',
         'parent_id' => 'nullable|exists:countries,id',
      ]);

      if ($validate->fails()) {
         return 'validatio error';
      }

      $name = ["ar" => $request->arName, "en" => $request->enName];
      $request->name = json_encode($name, JSON_UNESCAPED_UNICODE);

      $country = Country::create([
         'name' => json_decode($request->name),
         'phone_code' => $request->phone_code,
         'parent_id' => $request->parent_id
      ]);
      return $country;
   }

   public static function index($lang, $type)
   {
      self::createList();
      $request = ['lang' => $lang, 'type' => $type];

      $validate = \Validator::make($request, [
         'lang' => 'required|in:AR,EN',
         'type' => 'required|in:country,government,area',
      ]);

      if ($validate->fails()) {
         return 'validatio error';
      }

        $list = self::getCache('Areas');
        //return $list;
         switch ($type)
        {
          case 'country' : {
            $result =Arr::pluck($list,'id',$lang=='AR'?'arabicName':'englishName');
            return response()->json($result, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
            break;
         }
         case 'government': {
      
            $result=$list->pluck('governments')->flatten()->pluck('id');
            return response()->json($result, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
            break;

         }
         default : {
            return 'default'; break; 
         }



       }
      
   }

   public static function show($id)
   {
      $request = ['id' => $id];
 
      $validate = \Validator::make($request, [
         'id' => 'required|exists:countries,id',
      ]);     

      if ($validate->fails()) {
         abort(404,'Item not found');
      }
 
      return Country::find($id);
   }

   public static function delete($id)
   {
      $request = ['id' => $id];
 
      $validate = \Validator::make($request, [
         'id' => 'required|exists:countries,id',
      ]);     

      if ($validate->fails()) {
         abort(404,'Item not found');
      }
 
       $country=Country::find($id);
       $country->delete();
       if ($country) {
      return response()->json('Country deleted ',200);
       }
   }

   public static function restore($id)
   {
      $request = ['id' => $id];
 
      $validate = \Validator::make($request, [
         'id' => 'required|exists:countries,id',
      ]);     

      if ($validate->fails()) {
         abort(404,'Item not found');
      }

      $country=Country::withTrashed()->find($id)->restore();
      if($country) {
         return response()->json('item restored ',200);
      }
   }

   public static function edit(Request $request,$id)
   {
      $validate = \Validator::make(Arr::add($request->all(), 'id', $id), [
         'id'=>'required|exists:countries,id',  
         'arName' => 'required',
         'enName' => 'required',
         'phone_code' => 'nullable|unique:countries,phone_code,'.$id,
         'parent_id' => 'nullable|exists:countries,id,'.$id,
      ]);

      if ($validate->fails()) {
         return $validate->errors();
      }
      
      $country = Country::find($id);
      $name = json_decode($country->name);
      $name = ["ar" => $request->arName, "en" => $request->enName];
      $request->name = json_encode($name, JSON_UNESCAPED_UNICODE);

      $country = Country::update([
         'name' => json_decode($request->name),
         'phone_code' => $request->phone_code,
         'parent_id' => $request->parent_id
      ]);
      return $country;
   }
}


