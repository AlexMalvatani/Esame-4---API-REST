<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Episode;
use App\Models\Seasons;

class EpisodeController extends Controller
{
    public function index()
    {
        // Logica per ottenere tutti gli episodi
        $episodes = Episode::all();

        // Restituisci una risposta JSON
        return response()->json(['episodes' => $episodes], 200);
    }

    public function show($id)
    {
        // Logica per ottenere un episodio specifico
        $episode = Episode::findOrFail($id);

        // Restituisci una risposta JSON
        return response()->json(['episode' => $episode], 200);
    }

    public function store(Request $request)
    {
        // Logica per creare un nuovo episodio
        $episode = Episode::create($request->all());

        // Restituisci una risposta JSON
        return response()->json(['message' => 'Episode added succesfully'], 201);
    }

    public function update(Request $request, $id)
    {
        // Logica per aggiornare un episodio
        $episode = Episode::findOrFail($id);
        $episode->update($request->all());

        // Restituisci una risposta JSON
        return response()->json(['message' => 'Episode updated succesfully'], 200);
    }

    public function destroy($id)
    {
        // Logica per eliminare un episodio
        $episode = Episode::findOrFail($id);
        $episode->delete();

        // Restituisci una risposta JSON
        return response()->json(['message' => 'Episode deleted succesfully'], 200);
    }

    public function season()
    {
        return $this->belongsTo(Seasons::class, 'season_id');
    }

    public function startWatching($season, $seasonId, $episodeId)
    {
        // Verifica se la stagione esiste
        $seasonExists = Seasons::where('id', $seasonId)->exists();

        // Verifica se l'episodio esiste
        $episodeExists = Episode::where('id', $episodeId)->exists();

        // Se sia la stagione che l'episodio esistono, procedi con la visione
        if ($seasonExists && $episodeExists) {
            return 'Visioning Episode ' . ' ' . $season . ' ' . $seasonId . ' ' . $episodeId;
        } else {
            // Se la stagione o l'episodio non esistono, restituisci una risposta JSON con codice di stato 404
            return response()->json(['error' => 'Season or episode not found'], 404);
        }
    }
}
