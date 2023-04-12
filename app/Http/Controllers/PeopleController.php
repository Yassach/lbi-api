<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeopleRequest;
use App\Http\Resources\PeopleResource;
use App\Models\People;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PeopleController extends Controller
{
    const PERSON_NOT_FOUND = 'Person not found';

    /**
     * Display a listing of the resource.
     */
    public function index(): PeopleResource
    {
        $movies = People::query()
            ->paginate(10);

        return new PeopleResource($movies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PeopleRequest $request): PeopleResource
    {
        $movie = People::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'date_of_birth' => $request->date_of_birth,
            'nationality' => $request->nationality
        ]);

        return new PeopleResource($movie);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): PeopleResource|JsonResponse
    {
        try {
            $movie = People::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            return new JsonResponse(['error' => self::PERSON_NOT_FOUND], 404);
        }

        return new PeopleResource($movie);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PeopleRequest $request, int $id): JsonResponse|PeopleResource
    {
        if (Gate::denies('admin')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        try {
            $person = People::query()->findOrFail($id);

            $person->fill($request->validated())->save();

            return new PeopleResource($person);

        } catch (ModelNotFoundException) {
            return new JsonResponse(['error' => self::PERSON_NOT_FOUND], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Gate::denies('admin')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        try {
            $person = People::query()->findOrFail($id);

            return $person->delete();

        } catch (ModelNotFoundException) {
            return new JsonResponse(['error' => self::PERSON_NOT_FOUND], 404);
        }
    }
}
