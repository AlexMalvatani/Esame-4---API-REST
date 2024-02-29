<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\TvSeries;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\QueryException;

class PublicRoutesController extends Controller
{
    // RECENT RELEASES
    public function recent()
    {
        try {
            // Ultimi 3 film inseriti   
            $latestMovies = Movie::latest()->take(3)->get();

            // Ultime 3 serie tv
            $latestTvSeries = TvSeries::latest()->take(3)->get();

            // Combino i risultati
            $recentReleases = $latestMovies->concat($latestTvSeries);

            // Ordina in base alla data di creazione in ordine decrescente
            $recentReleases = $recentReleases->sortByDesc('created_at');

            return response()->json(['recent_releases' => $recentReleases], 200);
        } catch (QueryException $e) {
            // Gestisci eccezioni del database
            return response()->json(['error' => 'Database error', "error_message" => $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Gestisci altre eccezioni generiche
            return response()->json(['error' => 'Unexpected error', "error_message" => $e->getMessage()], 500);
        }
    }
}
