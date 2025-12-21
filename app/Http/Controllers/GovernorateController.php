<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use App\Models\City;
use App\Models\Governorate;
use App\Http\Requests\StoreGovernorateRequest;
use App\Http\Requests\UpdateGovernorateRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\GovernorateResource;
use PHPUnit\Framework\Constraint\Count;

class GovernorateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $governorates = Governorate::all();
        return response()->json([
            'status' => 200,
            'message' => 'governorates fetched successfully',
            'data' => GovernorateResource::collection($governorates)
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
        // $validated = $request->validate([
        //     'name' => ['required'],
        // ]);
        // $governorate = Governorate::create($validated);
        // return response()->json([
        //     'message'=>'created city succefully'
        // ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Governorate $governorate)
    {
        return response()->json([
            'status' => 200,
            'message' => "{$governorate->name}, Cities fetched successfully",
            'data' => [
                'id' => $governorate->id,
                'name' => $governorate->name,
                'cities' => CityResource::collection($governorate->cities)
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Governorate $governorate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGovernorateRequest $request, Governorate $governorate)
    {
        //
        // $validated = $request->validate([
        //     'name' => ['required'],
        // ]);
        // $governorate->update($validated);
        // return response()->json([
        //     'message'=>'updated city succefully'
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Governorate $governorate)
    {
        //
        // $governorate->deleteOrFail();
        // return response()->json([
        //     'message'=>'deleted governorate succefully'
        // ]);
    }
}
