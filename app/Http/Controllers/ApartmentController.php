<?php

namespace App\Http\Controllers;

use App\Http\Filters\ApartmentFilters;
use App\Models\Apartment;
use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ApartmentFilters $filters)
    {
        $userId = auth('sanctum')->id();

        $query = Apartment::with([
            'user',
            'city',
            'governorate',
            'images',
        ])
            ->withCount([
                'favoriteUsers as is_favorite' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
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
    // public function store(StoreApartmentRequest $request)
    // {
    //     $validated = $request->validated();
    //     $apartment = Apartment::create(array_merge($validated, [
    //         'user_id' => $request->user()->id, //get id from sanctum token
    //     ]));
    //     //authorize
    //     //use Auth user instead of parameter


    //     if ($request->hasFile('cover_image')) {
    //         $path = $request->file('cover_image')->store('apartments', 'public');

    //         $apartment->images()->create([
    //             'path' => 'storage/' . $path,
    //         ]);
    //     }

    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $path = $image->store('apartments', 'public');

    //             $apartment->images()->create([
    //                 'path' => 'storage/' . $path,
    //             ]);
    //         }
    //     }


    //     // return response()->json([
    //     //     'status' => 200,
    //     //     'message' => 'Apartment created successfully',
    //     //     'data' => new ApartmentResource($apartment->load(['user', 'governorate', 'city'])),
    //     // ]);
    //     return ApartmentResource::make(
    //         $apartment->load(['user', 'governorate', 'city', 'images'])
    //     )
    //         ->additional([
    //             'status' => 201,
    //             'message' => 'Apartment created successfully.',
    //         ])
    //         ->response()
    //         ->setStatusCode(201);
    // }

    public function store(StoreApartmentRequest $request)
    {
        $validated = $request->validated();

        $apartment = Apartment::create(array_merge($validated, [
            'user_id' => $request->user()->id,
        ]));

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('apartments', 'public');

            $apartment->images()->create([
                'path' => 'storage/' . $path,
                'is_cover' => true,
            ]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('apartments', 'public');

                $apartment->images()->create([
                    'path' => 'storage/' . $path,
                    'is_cover' => false,
                ]);
            }
        }

        return ApartmentResource::make(
            $apartment->load(['user', 'city', 'governorate', 'images'])
        )->additional([
                    'status' => 201,
                    'message' => 'Apartment created successfully.',
                ])->response()->setStatusCode(201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Apartment $apartment)
    {
        //
        return ApartmentResource::make(
            $apartment->load(['user', 'governorate', 'city', 'images'])
        )
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
    // public function update(UpdateApartmentRequest $request, Apartment $apartment)
    // {
    //     $validated = $request->validated();
    //     $apartment->update($validated);

    //     if ($request->hasFile('cover_image')) {
    //         $path = $request->file('cover_image')->store('apartments', 'public');
    //         $cover = $apartment->images()->orderBy('id')->first();

    //         if ($cover) {
    //             $cover->update(['path' => 'storage/' . $path]);
    //         } else {
    //             $cover = $apartment->images()->create(['path' => 'storage/' . $path]);
    //         }
    //     }

    //     $images = $apartment->images()->orderBy('id')->get();
    //     // $coverImage = $images->first();

    //     $images->skip(1)->each(function ($img) {
    //         $img->delete();
    //     });

    //     if ($request->hasFile('images')) {
    //         foreach ($request->file('images') as $image) {
    //             $path = $image->store('apartments', 'public');
    //             $apartment->images()->create([
    //                 'path' => 'storage/' . $path,
    //             ]);
    //         }
    //     }

    //     return ApartmentResource::make(
    //         $apartment->load(['user', 'governorate', 'city', 'images'])
    //     )->additional([
    //                 'status' => 201,
    //                 'message' => 'Apartment updated successfully.',
    //             ])->response()->setStatusCode(201);
    // }

    public function update(UpdateApartmentRequest $request, Apartment $apartment)
    {
        DB::transaction(function () use ($request, $apartment) {
        $validated = $request->validated();

        $apartment->update($validated);

            $coverImage = $apartment->images()->where('is_cover', true)->first();
            $otherImages = $apartment->images()->where('is_cover', false)->get();

            $keepPaths = $validated['keep_images'] ?? $otherImages->pluck('path')->toArray();

            $pathsToDelete = array_diff($otherImages->pluck('path')->toArray(), $keepPaths);
            if (!empty($pathsToDelete)) {
                $apartment->images()->whereIn('path', $pathsToDelete)->delete();
        }

            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $image) {
                $path = $image->store('apartments', 'public');

                $apartment->images()->create([
                    'path' => 'storage/' . $path,
                        'is_cover' => false,
                ]);
            }
        }
        });

        return ApartmentResource::make(
            $apartment->load(['user', 'city', 'governorate', 'images'])
        )->additional([
                    'status' => 200,
                    'message' => 'Apartment updated successfully.',
                ]);
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
    public function my(Request $request)
    {
        $userId = $request->user()->id;

        $apartments = Apartment::with(['user', 'governorate', 'city', 'images'])
            ->withCount([
                'favoriteUsers as is_favorite' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ])
            ->where('user_id', $userId)
            ->get();


        return ApartmentResource::collection($apartments)
            ->additional([
                'status' => 200,
                'message' => 'Apartments fetched successfully.',
            ])
            ->response()
            ->setStatusCode(200);
    }
    public function favorite(Request $request)
    {
        $user_id = $request->user()->id;

        $apartments = $request->user()
            ->favoriteApartments()
            ->with(['user', 'governorate', 'city', 'images'])
            ->withCount([
                'favoriteUsers as is_favorite' => function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                }
            ])
            ->get();
        return ApartmentResource::collection($apartments)
            ->additional([
                'status' => 200,
                'message' => 'Apartments fetched successfully.',
            ])
            ->response()
            ->setStatusCode(200);
    }
    public function add_favorite(Apartment $apartment, Request $request)
    {
        $user = $request->user();

        $user->favoriteApartments()->syncWithoutDetaching($apartment->id);

        return response()->json([
            'status' => 200,
            'message' => 'Apartment added to favorites successfully.',
        ]);
    }
    public function remove_favorite(Apartment $apartment, Request $request)
    {
        $user = $request->user();

        $user->favoriteApartments()->detach($apartment->id);

        return response()->json([
            'status' => 200,
            'message' => 'Apartment removed from favorites successfully.',
        ]);
    }

    public function notAvailableDates(Apartment $apartment, Request $request)
    {

        return response()->json([
            'status' => 200,
            'message' => $apartment->id . ' id Not Available Dates',
            'data' => $apartment->notAvailableDates(),
        ]);
    }
}

