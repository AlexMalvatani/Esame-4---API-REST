<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    // Logica per ottenere tutti gli utenti
    public function index()
    {
        $users = User::all();

        // Restituisci una risposta JSON
        return response(['users' => $users], 200);
    }

    // Logica per ottenere un utente specifico
    public function show($name)
    {
        $user = User::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }
        // Restituisci una risposta JSON
        return response()->json(['user' => $user], 200);
    }

    // Logica per aggiornare un utente
    public function update(Request $request, $name)
    {
        // Ottengo utente
        $user = User::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        // Backup vecchi dati utente
        $oldUserData = $user->toArray();

        // Aggiorno solo i dati inseriti dall'admin
        $user->fill($request->only(['name', 'email', 'password', 'avatar', 'bio', 'date_of_birth', 'country', 'city', 'address', 'credits', 'CF', 'role', 'first_name', 'last_name', 'vat_number']));

        // Cripta la nuova email con SHA-256 se è stata modificata
        if ($request->filled('email') && $request->email !== $oldUserData['email']) {
            $user->email = hash('sha256', $request->email);
            $user->email_address = $request->email;
        }

        // Cripta la nuova password con bcrypt se è stata modificata
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Salvo
        $user->save();

        // Ottengo campi aggiornati confrontandoli con quelli vecchi
        $upDateFields = array_diff_assoc($user->toArray(), $oldUserData);

        // Restituisci una risposta JSON con elenco campi aggiornati
        return response()->json([
            'message' => 'User updated succesfully',
            'updated_fields' => $upDateFields
        ], 200);
    }

    // Banna Utente
    public function banUser(Request $request, $id)
    {

        try {
            // Ottengo utente
            $user = User::find($id);

            if (!$user) {
                return response(['message' => 'User not found'], 404);
            }

            if ($user->banned) {
                return response(['message' => 'User alredy banned']);
            } else {
                $user->banned = 1;

                $user->save();

                return response(['message' => 'User banned successfully'], 200);
            }
        } catch (\Exception $e) {
            // Gestisci eventuali eccezioni
            return response(['error' => 'Error', $e->getMessage()]);
        }
    }

    // Sbanna Utente
    public function unbanUser(Request $request, $id)
    {

        try {
            // Ottengo utente
            $user = User::find($id);

            if (!$user) {
                return response(['message' => 'User not found'], 404);
            }

            if ($user->banned) {

                $user->banned = 0;

                $user->save();

                return response(['message' => 'User unbanned successfully'], 200);
            } else if (!$user->banned) {

                return response(['message' => 'User is not banned!']);
            }
        } catch (\Exception $e) {
            // Gestisci eventuali eccezioni
            return response(['error' => 'Error', $e->getMessage()]);
        }
    }

    // Elimina Utente
    public function destroy($id)
    {
        try {
            // Trovo l'utente
            $user = User::find($id);

            if (!$user) {
                return response(['message' => 'User not found'], 404);
            }

            // Soft Delete utente dalla tabella users
            $user->delete();

            // Soft delete l'utente nella tabella cripted
            DB::table('cripted')->where('user_id', $user->id)->update([
                'tries' => 0,
                'locked' => 0,
                'last_login_attempt' => null,
                'random_salt' => null,
                'jwt_token' => null,
            ]);

            // Soft delete l'utente nella tabella role_user
            DB::table('role_user')->where('user_id', $user->id)->update(['deleted_at' => now()]);

            // Restituisci una risposta JSON
            return response()->json(['message' => 'User ' . $user->name . ' with ID ' . $user->id . ' deleted succesfully'], 200);
        } catch (ModelNotFoundException $e) {
            // Gestisci l'eccezione se l'utente non esiste
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function restore($id)
    {
        // Trova l'utente eliminato
        $user = User::withTrashed()->where('id', $id)->first();

        if ($user) {
            // Ripristina l'utente
            $user->restore();

            // Ripristina anche nella tabella role_user
            DB::table('role_user')->where('user_id', $user->id)->update(['deleted_at' => null]);

            return response()->json(['message' => 'User ' . $user->name . ' restored successfully.']);
        } else {
            return response()->json(['error' => 'User not found.'], 404);
        }
    }
}
