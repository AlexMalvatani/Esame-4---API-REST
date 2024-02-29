<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TvSeries;
use App\Models\Category;
use App\Models\Episode;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // Per gestire l'errore dell'integer della categoria serie tv
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Seasons;

class TvSeriesController extends Controller
{
    // Lista Serie TV
    public function index()
    {

        $tvSeries = TvSeries::all();

        return response()->json(['tvSeries' => $tvSeries], 200);
    }

    // Serie TV Specifica
    public function show($id)
    {

        $tvSeries = TvSeries::find($id);

        if (!$tvSeries) {
            return response()->json(['error' => 'Tv Series not found'], 404);
        }

        return response()->json(['tvSeries' => $tvSeries], 200);
    }

    // Filtra Serie Tv Per Genere
    public function filterByCategory($genre)
    {
        // Trova la categoria
        $category = Category::where('name', $genre)->first();

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Ottieni i film associati alla categoria
        $tvseries = $category->TvSeries;

        if ($tvseries->isEmpty()) {
            return response()->json(['message' => 'No TV series found for the specified category'], 404);
        }

        return response()->json(['tv-series' => $tvseries], 200);
    }

    // Inserisci Nuova Serie TV
    public function storeNewTvSeries(Request $request)
    {

        $requiredFields = ['title', 'description', 'actors', 'director', 'year', 'category_id'];

        foreach ($requiredFields as $field) {

            if (!$request->has($field)) {

                throw ValidationException::withMessages([$field => 'The field ' . $field . ' is required!']);
            }
        }

        $checkIfExists = TvSeries::where('title', $request->input('title'))->first();

        if ($checkIfExists) {

            return response()->json(['message' => 'Tv Series already exists!'], 422);
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
            $tvseries = TvSeries::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'actors' => $request->input('actors'),
                'director' => $request->input('director'),
                'year' => $request->input('year'),
                'category_id' => $request->input('category_id'),
            ]);

            return response()->json(['message' => 'Tv Series ' . $tvseries->title . ' added succesfully'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Elimina Serie TV
    public function destroyTvSeries($id)
    {
        $tvseries = TvSeries::withTrashed()->find($id);

        if (!$tvseries) {

            return response()->json(['message' => 'Tv Series not found!'], 404);
        }

        if ($tvseries->trashed()) {

            return response()->json(['message' => 'Tv Series ' . $tvseries->title . ' is already deleted!']);
        }

        $tvseries->delete();

        return response()->json(['message' => 'Tv Series ' . $tvseries->title . ' succesfully deleted!'], 200);
    }

    // Restore Serie TV Eliminata
    public function restoreTvSeries($id)
    {
        $tvseries = TvSeries::withTrashed()->where('id', $id)->first();

        if (!$tvseries) {
            return response()->json(['message' => 'Tv Series not found!'], 404);
        }

        $tvseries->restore();

        return response()->json(['message' => 'Tv Series ' . $tvseries->title . ' successfully restored!'], 200);
    }

    // Lista Serie Tv Cancellate
    public function listDeletedTvSeries()
    {
        $deletedTvSeries = TvSeries::onlyTrashed()->get();

        if ($deletedTvSeries->isEmpty()) {
            return response()->json(['message' => 'No deleted Tv Series found!']);
        } else {

            return response()->json(['deleted_movies' => $deletedTvSeries]);
        }
    }

    // Modifica Serie TV
    public function modifyTvSeries(Request $request, $id)
    {

        try {
            $tvseries = TvSeries::find($id);

            if (!$tvseries) {
                return response()->json(['message' => 'Tv Series not found'], 404);
            }

            // Backup dati film
            $tvseriesDataBeforeUpdate = $tvseries->toArray();

            // Aggiorno solo i dati inseriti
            $tvseries->fill($request->only(['title', 'description', 'actors', 'director', 'year', 'category_id']));

            $tvseries->save();

            // Ottengo campi aggiornati confrontandoli con quelli vecchi
            $upDateFields = array_diff_assoc($tvseries->toArray(), $tvseriesDataBeforeUpdate);

            // Restituisco una risposta JSON con elenco campi aggiornati
            return response()->json([
                'message' => 'Tv Series ' . $tvseries->title . ' succesfully modified!',
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

    ##### STAGIONI #####

    // Inserisci Nuova Stagione Serie TV
    public function storeNewSeason(Request $request)
    {

        $requiredFields = ['number', 'description', 'tv_series_id'];

        foreach ($requiredFields as $field) {

            if (!$request->has($field)) {

                throw ValidationException::withMessages([$field => 'The field ' . $field . ' is required!']);
            }
        }

        // Verifica esistenza della stagione da inserire per non duplicare il record
        $checkIfExists = Seasons::where('number', $request->input('number'))->where('tv_series_id', $request->input('tv_series_id'))->first();

        if ($checkIfExists) {

            return response()->json(['message' => 'Season already exists!'], 422);
        }

        try {
            // Validazione dati
            $request->validate([
                'number' => 'required|integer',
                'description' => 'required|string',
                'tv_series_id' => 'required|integer',
            ]);

            // Inserimento
            $season = Seasons::create([
                'number' => $request->input('number'),
                'description' => $request->input('description'),
                'tv_series_id' => $request->input('tv_series_id'),
            ]);

            // Ottengo nome Tv Series per messaggio di errore
            $tvseriesName = $season->TvSeries->name;

            return response()->json(['message' => 'Season ' . $season->number . ' added succesfully to Tv Series ' . $tvseriesName], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Cancella Stagione
    public function deleteSeason($tv_series_id, $number, Request $request)
    {

        $season = Seasons::withTrashed()
            ->where('number', $number)
            ->where('tv_series_id', $tv_series_id)
            ->first();

        if (!$season) {

            return response()->json(['message' => 'Season not found!'], 404);
        }

        if ($season->trashed()) {

            return response()->json(['message' => 'Season is already deleted!']);
        }

        $season->delete();

        return response()->json(['message' => 'Season succesfully deleted!'], 200);
    }

    // Restore Stagione Cancellata
    public function restoreSeason($tv_series_id, $number)
    {
        $season = Seasons::withTrashed()
            ->where('number', $number)
            ->where('tv_series_id', $tv_series_id)
            ->first();

        if (!$season) {
            return response()->json(['message' => 'Season not found!'], 404);
        }

        $season->restore();

        return response()->json(['message' => 'Season successfully restored!'], 200);
    }

    // Lista Stagioni Cancellate
    public function listDeletedSeasons()
    {
        $deletedSeasons = Seasons::onlyTrashed()->get();

        if ($deletedSeasons->isEmpty()) {
            return response()->json(['message' => 'No deleted seasons found!']);
        } else {

            return response()->json(['deleted_seasons' => $deletedSeasons]);
        }
    }

    // Modifica Stagione
    public function modifySeason($tv_series_id, $number, Request $request)
    {

        try {
            $season = Seasons::where('number', $number)
                ->where('tv_series_id', $tv_series_id)
                ->first();

            if (!$season) {
                return response()->json(['message' => 'Season not found'], 404);
            }

            // Backup dati film
            $seasonDataBeforeUpdate = $season->toArray();

            // Aggiorno solo i dati inseriti
            $season->fill($request->only(['number', 'description', 'tv_series_id']));

            $season->save();

            // Ottengo campi aggiornati confrontandoli con quelli vecchi
            $upDateFields = array_diff_assoc($season->toArray(), $seasonDataBeforeUpdate);

            // Restituisco una risposta JSON con elenco campi aggiornati
            return response()->json([
                'message' => 'Season succesfully modified!',
                'updated_fields' => $upDateFields
            ], 200);
        } catch (QueryException $e) {

            if (Str::contains($e->getMessage(), 'Incorrect integer value')) {
                // Se l'errore è dovuto a un valore non valido per category_id
                return response()->json(['message' => 'Invalid value for number or tv_series_id'], 422);
            }

            // Gestisci altri errori
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {

            if (isset($season) && $season->wasChanged()) {
                $season->update($seasonDataBeforeUpdate);
            }

            // Gestisci altri tipi di eccezioni
            return response()->json(['message' => 'An unexpected error occurred'], 500);
        }
    }

    ##### EPISODI #####

    public function storeNewEpisode(Request $request)
    {

        $requiredFields = ['number', 'title', 'description', 'season_id'];

        foreach ($requiredFields as $field) {

            if (!$request->has($field)) {

                throw ValidationException::withMessages([$field => 'The field ' . $field . ' is required!']);
            }
        }

        // Verifica esistenza della stagione da inserire per non duplicare il record
        $checkIfExists = Episode::where('title', $request->input('title'))->where('season_id', $request->input('season_id'))->first();

        if ($checkIfExists) {

            return response()->json(['message' => 'Episode already exists!'], 422);
        }

        try {
            // Validazione dati
            $request->validate([
                'number' => 'required|integer',
                'title' => 'required|string',
                'description' => 'required|string',
                'season_id' => 'required|integer',
            ]);

            // Inserimento
            $episode = Episode::create([
                'number' => $request->input('number'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'season_id' => $request->input('season_id'),
            ]);


            return response()->json(['message' => 'Episode added succesfully !'], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Cancella Episodio
    public function deleteEpisode($number, $season_id)
    {

        $episode = Episode::withTrashed()
            ->where('number', $number)
            ->where('season_id', $season_id)
            ->first();

        if (!$episode) {

            return response()->json(['message' => 'Episode not found!'], 404);
        }

        if ($episode->trashed()) {

            return response()->json(['message' => 'Episode is already deleted!']);
        }

        $episode->delete();

        return response()->json(['message' => 'Episode succesfully deleted!'], 200);
    }

    // Restore Episodio Cancellato
    public function restoreEpisode($number, $season_id)
    {
        $episode = Episode::withTrashed()
            ->where('number', $number)
            ->where('season_id', $season_id)
            ->first();

        if (!$episode) {
            return response()->json(['message' => 'Episode not found!'], 404);
        }

        $episode->restore();

        return response()->json(['message' => 'Epsiode successfully restored!'], 200);
    }

    // Lista Episodi Cancellati
    public function listDeletedEpisodes()
    {
        $deletedEpisodes = Episode::onlyTrashed()->get();

        if ($deletedEpisodes->isEmpty()) {
            return response()->json(['message' => 'No deleted Episodes found!']);
        } else {

            return response()->json(['deleted_episodes' => $deletedEpisodes]);
        }
    }

    // Modifica Episodio
    public function modifyEpisode($number, $season_id, Request $request)
    {

        try {
            $episode = Episode::where('number', $number)
                ->where('season_id', $season_id)
                ->first();

            if (!$episode) {
                return response()->json(['message' => 'Season not found'], 404);
            }

            // Backup dati film
            $episodeDataBeforeUpdate = $episode->toArray();

            // Aggiorno solo i dati inseriti
            $episode->fill($request->only(['number', 'title', 'description', 'season_id']));

            $episode->save();

            // Ottengo campi aggiornati confrontandoli con quelli vecchi
            $upDateFields = array_diff_assoc($episode->toArray(), $episodeDataBeforeUpdate);

            // Restituisco una risposta JSON con elenco campi aggiornati
            return response()->json([
                'message' => 'Season succesfully modified!',
                'updated_fields' => $upDateFields
            ], 200);
        } catch (QueryException $e) {

            if (Str::contains($e->getMessage(), 'Incorrect integer value')) {
                // Se l'errore è dovuto a un valore non valido per category_id
                return response()->json(['message' => 'Invalid value for number or season_id'], 422);
            }

            // Gestisci altri errori
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {

            if (isset($season) && $season->wasChanged()) {
                $season->update($episodeDataBeforeUpdate);
            }

            // Gestisci altri tipi di eccezioni
            return response()->json(['message' => 'An unexpected error occurred'], 500);
        }
    }
}
