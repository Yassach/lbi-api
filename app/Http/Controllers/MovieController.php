<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieRequest;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{
    const MOVIE_NOT_FOUND = 'Movie not found';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::query()
            ->paginate(10);

        return new MovieResource($movies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MovieRequest $request): MovieResource
    {
        $movieRapidApi = $this->getRapidapiImageurl($request->title);

        $data = [
            'title' => $request->title,
            'duration' => $request->duration
        ];

        if ($movieRapidApi) {
            $data['url'] = $movieRapidApi['imageurl'][0];
        } elseif ($request->has('url')) {
            $data['url'] = $request->url;
        }

        $movie = Movie::create($data);

        return new MovieResource($movie);

    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse|MovieResource
    {
        try {
            $movie = Movie::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            return new JsonResponse(['error' => self::MOVIE_NOT_FOUND], 404);
        }

        return new MovieResource($movie);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MovieRequest $request, int $id): JsonResponse|MovieResource
    {
        if (Gate::denies('admin')) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
        try {
            $movie = Movie::query()->findOrFail($id);

            $movie->fill($request->validated())->save();

            return new MovieResource($movie);

        } catch (ModelNotFoundException) {
            return new JsonResponse(['error' => self::MOVIE_NOT_FOUND], 404);
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
            $movie = Movie::query()->findOrFail($id);

            return $movie->delete();

        } catch (ModelNotFoundException) {
            return new JsonResponse(['error' => self::MOVIE_NOT_FOUND], 404);
        }
    }

    /**
     * @param string $title
     * @return false|mixed|void
     */
    protected function getRapidapiImageurl(string $title)
    {
        $response = Http::withHeaders([
            "X-RapidAPI-Host" => "ott-details.p.rapidapi.com",
            "X-RapidAPI-Key" => env('RAPID_API_KEY')
        ])
            ->get("https://ott-details.p.rapidapi.com/search", [
                "title" => $title,
                "type" => "movie",
            ]);

        if ($response->failed()) {
            return false;
        }
        $movies = $response->json('results');

        foreach ($movies as $movie) {
            if (isset($movie['imageurl'])) {
                return $movie;
            }
        }
    }
}
