<?php

namespace App\Http\Controllers;

use App\Http\Filters\ApartmentFilters;
use App\Models\Apartment;
use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ApartmentFilters $filters)
    {
        $query = Apartment::query();
        $query = $filters->apply($query);

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 10);
        $apartments = $query->paginate($perPage);

        return response()->json([
            'status' => 200,
            'message' => 'Apartments fetched successfully',
            'pagination' => [
                'current_page' => $apartments->currentPage(),
                'per_page' => $apartments->perPage(),
                'total' => $apartments->total(),
                'last_page' => $apartments->lastPage(),
            ],
            'data' => ApartmentResource::collection($apartments),
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
    public function store(StoreApartmentRequest $request)
    {
        //
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users'],
            'country_id' => ['required', 'exists:countries'],
            'city_id' => ['required', 'exists:cities'],
            'title' => ['required'],
            'description' => ['required'],
            'price' => ['required', 'numeric'],

        ]);
        //authorize
        //use Auth user instead of parameter
        $apartment = Apartment::create($validated);
        return response()->json([
            'message'=>'appartment created sucessfully',
            'apartment'=>$apartment
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        //
        return response()->json([$apartment]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Apartment $apartment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApartmentRequest $request, Apartment $apartment)
    {
        //
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users'],
            'country_id' => ['required', 'exists:countries'],
            'city_id' => ['required', 'exists:cities'],
            'title' => ['required'],
            'description' => ['required'],
            'price' => ['required', 'numeric'],
        ]);
        //authorize
        //use Auth user instead of parameter
        $apartment->update($validated);
        return response()->json([
            'message'=>'appartment created sucessfully',
            'apartment'=>$apartment
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment)
    {
        //authorize
        $apartment->deleteOrFail();
        return response()->json(
            ['message'=>'apartmert deleted succefully']
        );
    }
    //--------------

}

