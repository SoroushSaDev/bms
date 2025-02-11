<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cities = City::with('Country')->when($request->has('country_id'), function ($query) use ($request) {
            $query->where('country_id', $request->get('country_id'));
        })->get();
        $cities->map(function (City $city) {
            $city->Translate();
        });
        return response()->json([
            'status' => 'success',
            'data' => $cities,
            'message' => 'Fetched cities successfully',
        ], 200);
    }

    public function store(CityRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $city = City::create([
               'country_id' => $request['country'],
               'name' => $request['name'],
            ]);
            DB::commit();
            $city->Translate();
            return response()->json([
                'status' => 'success',
                'data' => $city,
                'message' => __('city.created'),
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'data' => $exception->getMessage(),
                'message' => 'Error while storing cities',
            ], 500);
        }
    }

    public function show(City $city): JsonResponse
    {
        $city->Translate();
        return response()->json([
            'status' => 'success',
            'data' => $city,
            'message' => 'Fetched city successfully',
        ], 200);
    }

    public function update(City $city, CityRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $city->update([
                'country_id' => $request['country'],
                'name' => $request['name'],
            ]);
            DB::commit();
            $city->Translate();
            return response()->json([
                'status' => 'success',
                'data' => $city,
                'message' => __('city.updated'),
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'data' => $exception->getMessage(),
                'message' => 'Error while updating cities',
            ], 500);
        }
    }

    public function destroy(City $city): JsonResponse
    {
        DB::beginTransaction();
        try {
            $city->delete();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('city.deleted'),
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'data' => $exception->getMessage(),
                'message' => 'Error while deleting cities',
            ]);
        }
    }

    public function GetCountries()
    {
        $countries = Country::select(['id', 'en_name', 'fa_name'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $countries,
            'message' => 'Fetched countries successfully',
        ], 200);
    }
}
