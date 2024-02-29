<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // Per gestire l'errore dell'integer della categoria film
use Illuminate\Database\Eloquent\SoftDeletes;

class MovieController extends Controller
{

    // Lista Films
    public function index()
    {
        $movies = Movie::all();

        return response()->json(['movies' => $movies], 200);
    }

    // Visualizza Film Specifico
    public function show($id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json(['error' => 'Movie not found'], 404);
        }

        return response()->json(['movie' => $movie], 200);
    }

    // Aggiungi Film
    public function storeNewMovie(Request $request)
    {
        $requiredFields = ['title', 'description', 'actors', 'director', 'year', 'category_id'];

        foreach ($requiredFields as $field) {

            if (!$request->has($field)) {

                throw ValidationException::withMessages([$field => 'The field ' . $field . ' is required!']);
            }
        }

        $checkIfExists = Movie::where('title', $request->input('title'))->first();

        if ($checkIfExists) {

            return response()->json(['message' => 'Film already exists!'], 422);
        }

        try {
            // Validazione dati
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'actors' => 'required|string|max:500',
                'director' => 'required|string|max:255',
                'year' => 'required|integer',
                'category_id' => 'required|integer',
            ]);

            // Inserimento
            $movie = Movie::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'actors' => $request->input('actors'),
                'director' => $request->input('director'),
                'year' => $request->input('year'),
                'category_id' => $request->input('category_id'),
            ]);

            return response()->json(['message' => 'Film ' . $movie->title . ' added succesfully'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Modifica Film
    public function modifyMovie(Request $request, $id)
    {
        try {
            $movie = Movie::find($id);

            if (!$movie) {
                return response()->json(['message' => 'Movie not found'], 404);
            }

            // Backup dati film
            $movieDataBeforeUpdate = $movie->toArray();

            // Aggiorno solo i dati inseriti
            $movie->fill($request->only(['title', 'description', 'actors', 'director', 'year', 'category_id']));

            $movie->save();

            // Ottengo campi aggiornati confrontandoli con quelli vecchi
            $upDateFields = array_diff_assoc($movie->toArray(), $movieDataBeforeUpdate);

            // Restituisco una risposta JSON con elenco campi aggiornati
            return response()->json([
                'message' => 'Movie succesfully modified!',
                'updated_fields' => $upDateFields
            ], 200);
        } catch (QueryException $e) {

            if (Str::contains($e->getMessage(), 'Incorrect integer value')) {
                // Se l'errore è dovuto a un valore non valido per category_id
                return response()->json(['message' => 'Invalid value for category_id'], 422);
            }

            // Gestisci altri errori
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {

            // Gestisci altri tipi di eccezioni
            return response()->json(['message' => 'An unexpected error occurred'], 500);
        }
    }

    // Elimina Film
    public function destroyMovie($id)
    {

        $movie = Movie::withTrashed()->find($id);

        if (!$movie) {

            return response()->json(['message' => 'Film not found!'], 404);
        }

        if ($movie->trashed()) {

            return response()->json(['message' => 'Film ' . $movie->title . ' is already deleted!']);
        }

        $movie->delete();

        return response()->json(['message' => 'Film ' . $movie->title . ' succesfully deleted!'], 200);
    }

    // Restore Di Un Film Cancellato
    public function restoreMovie($id)
    {
        $movie = Movie::withTrashed()->where('id', $id)->first();

        if (!$movie) {
            return response()->json(['message' => 'Film not found!'], 404);
        }

        $movie->restore();

        return response()->json(['message' => 'Film ' . $movie->title . ' successfully restored!'], 200);
    }

    // Lista Film Cancellati
    public function listDeletedMovies()
    {
        $deletedMovies = Movie::onlyTrashed()->get();

        if ($deletedMovies->isEmpty()) {
            return response()->json(['message' => 'No deleted movies found!']);
        } else {

            return response()->json(['deleted_movies' => $deletedMovies]);
        }
    }

    // Visualizza Film (provvisorio)
    public function startWatching($id)
    {
        return 'Visioning Movie ' . $id;
    }

    // Filtra Films Per Genere
    public function filterByCategory($genre)
    {
        // Trova la categoria
        $category = Category::where('name', $genre)->first();

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Ottieni i film associati alla categoria
        $movies = $category->movies;

        if ($movies->isEmpty()) {
            return response()->json(['message' => 'No movies found for the specified category'], 404);
        }

        return response()->json(['movies' => $movies], 200);
    }
}
