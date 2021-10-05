<?php

namespace MEDICAL\Countries\Controllers;

use Illuminate\Http\Request;
use  Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use MEDICAL\Countries\Models\Country;
use Laravel\Lumen\Routing\Controller as BaseController;
use MEDICAL\Countries\Resources\CountryResource;
use MEDICAL\Countries\Resources\GovernResource;
use MEDICAL\Countries\Resources\AreaResource;
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
         return json_decode(app('redis')->get($key), true);
      } else
         abort(404, 'Cahce not found');
   }

   public static function createCountryList()
   {
      $countries = Country::select('id', 'phone_code', DB::raw('name->>"$.ar" as arabicName , name->>"$.en" as englishName'))
         ->wherenull('parent_id')->get();

      $countries1 = CountryResource::collection($countries);
      $encode = json_encode($countries1);
      return self::setCache($encode, 'countries', 30 * 24 * 60 * 60);
   }

   public static function createGovernList($countryId)
   {
      $governs = Country::select('id', 'phone_code', DB::raw('name->>"$.ar" as arabicName , name->>"$.en" as englishName'))
         ->where('parent_id', $countryId)->get();

      $governs1 = GovernResource::collection($governs);
      $encode = json_encode($governs1);
      return self::setCache($encode, 'governs-' . $countryId, 30 * 24 * 60 * 60);
   }

   public static function createAreaList($governId)
   {
      $areas = Country::select('id', 'phone_code', DB::raw('name->>"$.ar" as arabicName , name->>"$.en" as englishName'))
         ->where('parent_id', $governId)->get();

      $areas1 = AreaResource::collection($areas);
      $encode = json_encode($areas1);
      return self::setCache($encode, 'area-' . $governId, 30 * 24 * 60 * 60);
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

   public static function countryIndex($lang)
   {
      $request = ['lang' => $lang];

      $validate = \Validator::make($request, [
         'lang' => 'required|in:AR,EN',
      ]);

      if ($validate->fails()) {
         return $validate->errors();
      }

      $countryList = self::getCache('countries');
      if (empty($countryList)) {
         self::createCountryList();
         $countryList = self::getCache('countries');
      }


      //$result = Arr::pluck($countryList, 'id', $lang == 'AR' ? 'arabicName' : 'englishName');
      return response()->json($countryList, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
   }

   public static function governIndex($lang, $countryId)
   {
      $request = ['lang' => $lang, 'countryId' => $countryId];

      $validate = \Validator::make($request, [
         'lang' => 'required|in:AR,EN',
         'countryId' => 'nullable|exists:countries,id'
      ]);

      if ($validate->fails()) {
         return $validate->errors();
      }

      $governList = self::getCache('governs-' . $countryId);
      if (empty($governList)) {
         self::createGovernList($countryId);
         $governList = self::getCache('governs-' . $countryId);
      }


      // $result = Arr::pluck($governList, 'id', $lang == 'AR' ? 'arabicName' : 'englishName');
      return response()->json($governList, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
   }

   public static function areaIndex($lang, $governId)
   {
      $request = ['lang' => $lang, 'governId' => $governId];

      $validate = \Validator::make($request, [
         'lang' => 'required|in:AR,EN',
         'governId' => 'nullable|exists:countries,id'
      ]);

      if ($validate->fails()) {
         return $validate->errors();
      }

      $areaList = self::getCache('area-' . $governId);
      if (empty($areaList)) {
         self::createAreaList($governId);
         $areaList = self::getCache('area-' . $governId);
      }

      // $result = Arr::pluck($governList, 'id', $lang == 'AR' ? 'arabicName' : 'englishName');
      return response()->json($areaList, 200, array('Content-Type' => 'application/json;charset=utf8'), JSON_UNESCAPED_UNICODE);
   }

   public static function show($id)
   {
      $request = ['id' => $id];

      $validate = \Validator::make($request, [
         'id' => 'required|exists:countries,id',
      ]);

      if ($validate->fails()) {
         abort(404, 'Item not found');
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
         abort(404, 'Item not found');
      }

      $country = Country::find($id);
      $country->delete();
      if ($country) {
         return response()->json('Country deleted ', 200);
      }
   }

   public static function restore($id)
   {
      $request = ['id' => $id];

      $validate = \Validator::make($request, [
         'id' => 'required|exists:countries,id',
      ]);

      if ($validate->fails()) {
         abort(404, 'Item not found');
      }

      $country = Country::withTrashed()->find($id)->restore();
      if ($country) {
         return response()->json('item restored ', 200);
      }
   }

   public static function edit(Request $request, $id)
   {
      $data['id'] = $id;
      $data['arName'] = $request->input('arName');
      $data['enName'] = $request->input('enName');
      $data['phone_code'] = $request->input('phone_code');
      $data['parent_id'] = $request->input('parent_id');

      $validate = \Validator::make($data, [
         'id' => 'required|exists:countries,id',
         'arName' => 'required',
         'enName' => 'required',
         'phone_code' => 'nullable|unique:countries,phone_code,' . $id,
         'parent_id' => 'nullable|exists:countries,id,' . $id,
      ]);

      // if ($validate->fails()) {
      //    return 'error';
      // }

      $name = ["ar" =>  $data['arName'], "en" => $data['enName']];
      $data['name']= json_encode($name, JSON_UNESCAPED_UNICODE);

      $country = Country::find($id)->update([
         'name' => $data['name'] ,
         'phone_code' => $data['phone_code'],
         'parent_id' => $data['parent_id']
      ]);
      return $country;
   }
}