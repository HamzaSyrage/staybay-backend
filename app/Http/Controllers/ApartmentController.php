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
        $query = Apartment::with([
            'user',
            'city',
            'governorate',
            'images',
        ]);

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
        // $validated = $request->validate([
        //     'user_id' => ['required', 'exists:users'],
        //     'governorate_id' => ['required', 'exists:governorates'],
        //     'city_id' => ['required', 'exists:cities'],
        //     'title' => ['required'],
        //     'description' => ['required'],
        //     'price' => ['required', 'numeric'],
        // ]);
        $validated = $request->validated();
        // dd(auth('sanctum')->id());
        //?$request->user()->id same as auth()->id() but it shows no error in vs code
        //? i think using auth('sanctum')->id() is better for api and shows no error as well in vs code
        //? we have alot of options to get the authenticated user id i hate that
        $apartment = Apartment::create(array_merge($validated, [
            'user_id' => $request->user()->id, //get id from sanctum token
        ]));


        //authorize
        //use Auth user instead of parameter

        // return response()->json([
        //     'status' => 200,
        //     'message' => 'Apartment created successfully',
        //     'data' => new ApartmentResource($apartment->load(['user', 'governorate', 'city'])),
        // ]);
        return ApartmentResource::make($apartment->load(['user', 'governorate', 'city']))
            ->additional([
            'status' => 201,
            'message' => 'Apartment created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        //
        return ApartmentResource::make($apartment->load(['user', 'governorate', 'city']))
            ->additional([
                'status' => 200,
                'message' => 'Apartment fetched successfully.',
            ])
            ->response()
            ->setStatusCode(200);
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
        // $validated = $request->validate([
        //     'user_id' => ['required', 'exists:users'],
        //     'governorate_id' => ['required', 'exists:governorates'],
        //     'city_id' => ['required', 'exists:cities'],
        //     'title' => ['required'],
        //     'description' => ['required'],
        //     'price' => ['required', 'numeric'],
        // ]);
        $validated = $request->validated();


        //authorize
        //use Auth user instead of parameter
        $apartment->update($validated);
        return ApartmentResource::make($apartment)->additional([
            'status' => 201,
            'message' => 'Apartment updated successfully.',
        ])->response()->setStatusCode(201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Apartment $apartment)
    {
        //authorize
        $apartment->deleteOrFail();
        return response()->json([
            'status' => 200,
            'message' => 'Apartment deleted successfully',
            'data' => null,
        ]);
    }
    //

}

