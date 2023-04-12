<?php

namespace App\Http\Controllers;

use App\Http\Requests\MoviePeopleRequest;
use App\Http\Resources\MoviePeopleResource;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use App\Models\MoviePeople;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MoviePeopleController extends Controller
{
    const MOVIE_NOT_FOUND = 'Movie not found';

    /**
     * Store a newly created resource in storage.
     */
    public function store(MoviePeopleRequest $request, int $id)
    {
        if (Gate::denies('admin')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        try {
            $movie = Movie::query()->findOrFail($id);

            MoviePeople::create([
                'movie_id' => $id,
                'people_id' => $request->people_id,
                'role' => $request->role,
                'significance' => $request->significance,
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
    public function show(string $id): MoviePeopleResource|JsonResponse
    {
        try {
            $movie = Movie::query()->findOrFail($id);

            $movie->people->makeHidden('pivot');

            return new MoviePeopleResource($movie);

        } catch (ModelNotFoundException) {
            return new JsonResponse(['error' => self::MOVIE_NOT_FOUND], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MoviePeopleRequest $request, int $id, int $people): JsonResponse
    {
        if (Gate::denies('admin')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        try {
            DB::table('movie_people')
                ->whereMovieId($id)
                ->wherePeopleId($people)
                ->update($request->validated());

            return response()->json(['message' => 'Record updated']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, int $people)
    {
        if (Gate::denies('admin')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        try {
            DB::table('movie_people')
                ->whereMovieId($id)
                ->wherePeopleId($people)
                ->delete();

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
