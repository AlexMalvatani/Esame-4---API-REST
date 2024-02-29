<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\UserFavourites;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class UserFavouritesController extends Controller
{

    // Aggiungi ai preferiti
    public function addItem($type, $item_id)
    {
        $token = JWTAuth::parseToken();

        try {
            $user = $token->authenticate();

            $userId = $user->id;

            // Verifico se l'item è già presente nella sua lista preferiti
            $existingItem = UserFavourites::where('user_id', $userId)
                ->where($type . '_id', $item_id)
                ->where('item_type', $type)
                ->first();

            if ($existingItem) {

                return response()->json(['message' => 'Item already in favourites']);
            }

            $trashedInstances = DB::table('user_favourites')
                ->whereNotNull('deleted_at')
                ->where($type . '_id', $item_id)
                ->where('item_type', $type)
                ->where('user_id', $userId)
                ->get();

            $trashedInstance = $trashedInstances->first();

            if ($trashedInstance) {
                DB::table('user_favourites')
                    ->where('id', $trashedInstance->id)
                    ->update(['deleted_at' => null]);

                return response()->json(['message' => 'Item added to favourites']);
            } else {
                return response()->json(['error' => 'Item not found or not trashed']);
            }

            if ($type === 'movies') {

                UserFavourites::create([
                    'user_id' => $userId,
                    'movies_id' => $item_id,
                    'item_type' => $type
                ]);
            } elseif ($type === 'tv_series') {

                UserFavourites::create([
                    'user_id' => $userId,
                    'tv_series_id' => $item_id,
                    'item_type' => $type
                ]);
            }

            return response()->json(['message' => 'Item added to favourites']);
        } catch (\Exception $e) {
            return Response::json(['error' => 'Error processing the request.' . $e->getMessage()], 500);
        }
    }

    // Rimuovi dai preferiti
    public function deleteFavourite($type, $item_id)
    {
        $token = JWTAuth::parseToken();

        $user = $token->authenticate();

        $userId = $user->id;

        $item = UserFavourites::withTrashed()
            ->where('user_id', $userId)
            ->where('item_type', $type)
            ->where($type . '_id', $item_id)
            ->first();

        if (!$item) {

            return response()->json(['message' => 'Item not found!'], 404);
        }

        if ($item->trashed()) {

            return response()->json(['message' => 'Item is already deleted!']);
        }

        $item->delete();

        return response()->json(['message' => 'Item succesfully removed from favourites!'], 200);
    }

    public function listFavourites()
    {
        $favouritesList = UserFavourites::all();

        if (!$favouritesList->isEmpty()) {
        return response()->json(['Favourites' => $favouritesList]);
        }
        return response()->json(['Favourites' => 'No items in favourites!']);
        
    }
}
