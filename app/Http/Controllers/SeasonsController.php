<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TvSeries;

class SeasonsController extends Controller
{
    public function seasons($id)
    {
        // Trova la serie TV per ID
        $tvSeries = TvSeries::find($id);

        if (!$tvSeries) {
            return response()->json(['error' => 'Tv Series not found'], 404);
        }

        // Ottieni le stagioni della serie TV
        $seasons = $tvSeries->seasons;

        // Restituisci una risposta JSON con le stagioni
        return response()->json(['seasons' => $seasons], 200);
    }

    public function seasonDetails($seriesId, $seasonNumber)
    {
        // Trova la serie TV per ID
        $tvSeries = TvSeries::find($seriesId);

        if (!$tvSeries) {
            return response()->json(['error' => 'Tv Series not found'], 404);
        }
        
        // Trova la stagione specifica della serie TV
        $season = $tvSeries->seasons()->where('number', $seasonNumber)->first();

        if (!$season) {
            return response()->json(['error' => 'Season not found'], 404);
        }

        // Ottieni gli episodi della stagione
        $episodes = $season->episodes;

        // Restituisci una risposta JSON con le specifiche della stagione
        return response()->json(['season' => $season, 'episodes' => $episodes], 200);
    }
}
