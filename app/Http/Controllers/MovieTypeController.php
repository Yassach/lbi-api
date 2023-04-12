<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieTypeRequest;
use App\Http\Resources\MovieResource;
use App\Http\Resources\MovieTypeResource;
use App\Models\Movie;
use App\Models\MovieType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MovieTypeController extends Controller
{
    const MOVIE_NOT_FOUND = 'Movie not found';

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieTypeRequest $request, int $id)
    {
        if (Gate::denies('admin')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        try {
            $movie = Movie::query()->findOrFail($id);

            MovieType::create([
                'movie_id' => $id,
                'type_id' => $request->type,
            ]);

            return new MovieResource($movie);

        } catch (ModelNotFoundException) {
            return new JsonResponse(['error' => self::MOVIE_NOT_FOUND], 404);
        } catch (QueryException) {
            return new JsonResponse(['error' => 'Duplicated entry '], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse|MovieTypeResource
    {
        try {
            $movie = Movie::query()->findOrFail($id);

            $movie->types->makeHidden('pivot');

            return new MovieTypeResource($movie);

        } catch (ModelNotFoundException) {
            return new JsonResponse(['error' => self::MOVIE_NOT_FOUND], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieTypeRequest $request, int $id, int $type): JsonResponse
    {
        if (Gate::denies('admin')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        try {
            DB::table('movie_type')
                ->whereMovieId($id)
                ->whereTypeId($type)
                ->update(['type_id' => $request->type]);

            return response()->json(['message' => 'Record updated']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, int $type)
    {
        if (Gate::denies('admin')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        try {
            DB::table('movie_type')
                ->whereMovieId($id)
                ->whereTypeId($type)
                ->delete();

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}

