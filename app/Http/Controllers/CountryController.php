<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Models\City;
use App\Models\Country;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $countries = Country::all();
        return response()->json([
            'countries'=>$countries
        ]);
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
        $validated = $request->validate([
            'name' => ['required'],
        ]);
        $country = Country::create($validated);
        return response()->json([
            'message'=>'created city succefully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country)
    {
        //
        return response()->json([
            $country
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountryRequest $request, Country $country)
    {
        //
        $validated = $request->validate([
            'name' => ['required'],
        ]);
        $country->update($validated);
        return response()->json([
            'message'=>'updated city succefully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        //
        $country->deleteOrFail();
        return response()->json([
            'message'=>'deleted country succefully'
        ]);
    }
}
