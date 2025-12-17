<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\CityResource;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //! i think we only need this
    public function index()
    {
        //
        $cities = City::all();
        return CityResource::collection($cities);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCityRequest $request)
    {
        //
        // $validated = $request->validate([
        //     'name' => ['required'],
        //     'country_id' => ['required', 'exists:countries'],
        // ]);
        // $city = City::create($validated);
        // return response()->json([
        //     'message'=>'created city succefully'
        // ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        //
        // return response()->json([
        //     $city
        // ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, City $city)
    {
        //
        // $validated = $request->validate([
        //     'name' => ['required'],
        //     'country_id' => ['required', 'exists:countries'],
        // ]);
        // $city->update($validated);
        // return response()->json([
        //     'message'=>'edited city succefully'
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        //
        // $city->deleteOrFail();
        // return response()->json([
        //     'city'=>'city deleted succefully'
        // ]);
    }
}
