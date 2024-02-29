<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class UserProfileController extends Controller
{

    // Visualizza Profile Utente
    public function showProfile(Request $request)
    {
        // Verifica la presenza del token nelle intestazioni della richiesta
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Token not found!!'], 401);
        } else {

            // Prendo il token
            $tokenTemp = $request->header('Authorization');
            $jwtToken = str_replace('Bearer ', '', $tokenTemp);

            // Trovo l'utente nella tabella cripted con il token
            $userCripted = DB::table('cripted')->where('jwt_token', $jwtToken)->first();

            // Se il token è presente
            if ($userCripted) {

                // Trovo lo user nella tabella "users"
                $user = DB::table('users')->where('id', $userCripted->user_id)->first();

                // Se l'utente c'è
                if ($user) {

                    // Ritorno il profilo dell'utente
                    return response()->json(['Your Profile: ' => $user]);
                } else {

                    // Se l'utente non c'è ritorno errore
                    return response()->json(['message' => 'No User Found!'], 404);
                }
            }
        }
    }

    // Soft Delete User Profile
    public function deleteProfile(Request $request)
    {
        // Verifica la presenza del token nelle intestazioni della richiesta
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Token not found!!'], 401);
        } else {

            // Prendo il token
            $tokenTemp = $request->header('Authorization');
            $jwtToken = str_replace('Bearer ', '', $tokenTemp);

            // Trovo l'utente nella tabella cripted con il token
            $userCripted = DB::table('cripted')->where('jwt_token', $jwtToken)->first();

            // Se il token è presente
            if ($userCripted) {

                // Trovo lo user nella tabella "users"
                $user = User::find($userCripted->user_id);

                // Se l'utente c'è
                if ($user) {

                    // Soft Delete utente dalla tabella users
                    $user->delete();

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
                    return response()->json(['message' =>  $user->name . ' Your profile has been succesfully deleted!!'], 200);
                } else {

                    // Se l'utente non c'è ritorno errore
                    return response()->json(['message' => 'No User Found!'], 404);
                }
            }
        }
    }

    // Modifica Profilo Utente
    public function modifyProfile(Request $request)
    {
        // Verifica la presenza del token nelle intestazioni della richiesta
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Token not found!!'], 401);
        } else {

            // Prendo il token
            $tokenTemp = $request->header('Authorization');
            $jwtToken = str_replace('Bearer ', '', $tokenTemp);

            // Trovo l'utente nella tabella cripted con il token
            $userCripted = DB::table('cripted')->where('jwt_token', $jwtToken)->first();

            // Se il token è presente
            if ($userCripted) {

                // Trovo lo user nella tabella "users"
                $user = User::where('id', $userCripted->user_id)->first();

                // Se l'utente c'è
                if ($user) {

                    // Backup vecchi dati utente
                    $oldUserData = $user->toArray();

                    // Aggiorno solo i dati inseriti dall'admin
                    $user->fill($request->only(['name', 'email', 'avatar', 'bio', 'date_of_birth', 'country', 'city', 'address', 'CF', 'first_name', 'last_name', 'vat_number']));

                    // Cripta la nuova email con SHA-256 se è stata modificata
                    if ($request->filled('email') && $request->email !== $oldUserData['email']) {
                        $user->email = hash('sha256', $request->email);
                        $user->email_address = $request->email;
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
                } else {

                    // Se l'utente non c'è ritorno errore
                    return response()->json(['message' => 'No User Found!'], 404);
                }
            }
        }
    }

    // Update Username
    public function updateUsername(Request $request)
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Token not found!!'], 401);
        } else {

            // Prendo il token
            $tokenTemp = $request->header('Authorization');
            $jwtToken = str_replace('Bearer ', '', $tokenTemp);

            // Trovo l'utente nella tabella cripted con il token
            $userCripted = DB::table('cripted')->where('jwt_token', $jwtToken)->first();

            // Se il token è presente
            if ($userCripted) {

                // Trovo lo user nella tabella "users"
                $user = DB::table('users')->where('id', $userCripted->user_id)->first();

                // Converto l'oggetto stdClass in un'istanza del modello User
                $userModel = new User();
                $userModel->exists = true; // Segnalo che l'oggetto esiste nel database
                $userModel->setRawAttributes((array)$user, true);

                // Se l'utente c'è
                if ($userModel) {
                    $userModel->update([
                        'name' => $request->input('name'),
                    ]);

                    // Restituisci una risposta JSON
                    return response()->json(['message' => 'Username updated successfully'], 200);
                } else {
                    return response()->json(['message' => 'User not found!'], 404);
                }
            }
        }
    }

    // Cambio indirizzo e-mail 
    public function updateEmail(Request $request)
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Token not found!!'], 401);
        } else {

            // Prendo il token
            $tokenTemp = $request->header('Authorization');
            $jwtToken = str_replace('Bearer ', '', $tokenTemp);

            // Trovo l'utente nella tabella cripted con il token
            $userCripted = DB::table('cripted')->where('jwt_token', $jwtToken)->first();

            // Se il token è presente
            if ($userCripted) {

                // Trovo lo user nella tabella "users"
                $user = DB::table('users')->where('id', $userCripted->user_id)->first();

                // Converto l'oggetto stdClass in un'istanza del modello User
                $userModel = new User();
                $userModel->exists = true; // Segnalo che l'oggetto esiste nel database
                $userModel->setRawAttributes((array)$user, true);

                // Se l'utente c'è
                if ($userModel) {

                    $request->validate([
                        'email' => 'required|email|unique:users,email',
                    ]);

                    $newEmail = hash('sha256', $request->input('email'));
                    $userModel->update([
                        'email' => $newEmail,
                        'email_address' => $request->input('email'),
                    ]);

                    // Restituisci una risposta JSON
                    return response()->json(['message' => 'E-mail address updated successfully'], 200);
                } else {
                    return response()->json(['message' => 'User not found!'], 404);
                }
            }
        }
    }

    public function updatePassword(Request $request)
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Token not found!!'], 401);
        } else {
            // Prendo il token
            $tokenTemp = $request->header('Authorization');
            $jwtToken = str_replace('Bearer ', '', $tokenTemp);

            // Trovo l'utente nella tabella cripted con il token
            $userCripted = DB::table('cripted')->where('jwt_token', $jwtToken)->first();

            // Se il token è presente
            if ($userCripted) {

                // Trovo lo user nella tabella "users"
                $user = User::find($userCripted->user_id);

                // Se l'utente c'è
                if ($user) {
                    $request->validate([
                        'password' => 'required|min:8', // Puoi aggiungere ulteriori regole di validazione
                    ]);

                    $newPassword = bcrypt($request->input('password'));
                    $user->update([
                        'password' => $newPassword,
                    ]);

                    // Restituisci una risposta JSON
                    return response()->json(['message' => 'Password updated successfully'], 200);
                } else {
                    return response()->json(['message' => 'User not found!'], 404);
                }
            }
        }
    }

    // Update Credits
    public function updateCredits(Request $request)
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Token not found!!'], 401);
        } else {
            // Prendo il token
            $tokenTemp = $request->header('Authorization');
            $jwtToken = str_replace('Bearer ', '', $tokenTemp);

            // Trovo l'utente nella tabella cripted con il token
            $userCripted = DB::table('cripted')->where('jwt_token', $jwtToken)->first();

            // Se il token è presente
            if ($userCripted) {
                // Trovo lo user nella tabella "users"
                $user = DB::table('users')->where('id', $userCripted->user_id)->first();

                // Converto l'oggetto stdClass in un'istanza del modello User
                $userModel = new User();
                $userModel->exists = true; // Segnalo che l'oggetto esiste nel database
                $userModel->setRawAttributes((array)$user, true);

                // Se l'utente c'è
                if ($userModel) {
                    // Validazione del campo 'credits'
                    $request->validate([
                        'credits' => 'required|integer', // Aggiungi eventuali regole di validazione per i credits
                    ]);

                    // Aggiornamento del campo 'credits'
                    $userModel->update([
                        'credits' => $request->input('credits'),
                    ]);

                    // Restituisci una risposta JSON
                    return response()->json(['message' => 'Credits updated successfully'], 200);
                } else {
                    return response()->json(['message' => 'User not found!'], 404);
                }
            }
        }
    }
}
