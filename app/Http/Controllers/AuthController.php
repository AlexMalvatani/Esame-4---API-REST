<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Roles;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use App\Models\Cripted;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    // REGISTRAZIONE UTENTE SEMPLICE
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:6',
            'avatar' => 'required|string',
            'bio' => 'required|string',
            'date_of_birth' => 'required|date',
            'country' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'CF' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'vat_number' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Verifica se esiste già un utente con la stessa email criptata
            $existingUser = User::where('email', hash('sha256', $request->email))->first();

            if ($existingUser) {
                Log::error('Duplicate entry error during user registration: Email already exists.');
                return response()->json(['error' => 'Email already exists.'], 422);
            }

            $roleId = Roles::where('name', 'user')->first()->id;

            $user = User::create([
                'name' => $request->name,
                'email' => hash('sha256', $request->email),
                'password' => bcrypt($request->password),
                'avatar' => $request->avatar,
                'bio' => $request->bio,
                'date_of_birth' => $request->date_of_birth,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'CF' => $request->CF,
                'credits' => 10,
                'role' => 'user',
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'vat_number' => $request->vat_number,
                'email_address' => $request->email,
            ]);

            $user->roles()->attach($roleId);

            return response()->json(['message' => 'User successfully registered!', 'user' => $user], 201);
        } catch (\Exception $e) {
            Log::error('Unexpected error during user registration: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error.'], 500);
        }
    }

    // CREA UTENTE ADMIN (SOLO PER ADMIN)
    public function createAdmin(Request $request)
    {
        // Validazione delle richieste e creazione di un nuovo utente admin
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'date_of_birth' => 'nullable|date',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'CF' => 'nullable|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'vat_number' => 'nullable|string'
        ]);

        try {
            // Verifica se esiste già un utente con la stessa email criptata
            $existingUser = User::where('email', hash('sha256', $request->email))->first();

            if ($existingUser) {
                Log::error('Duplicate entry error during user registration: Email already exists.');
                return response()->json(['error' => 'Email already exists.'], 422);
            }

            $roleId = Roles::where('name', 'admin')->first()->id;

            // Inserisci l'utente admin nel database
            $admin = User::create([
                'name' => $request->name,
                'email' => hash('sha256', $request->email),
                'password' => bcrypt($request->password),
                'avatar' => 'No avatar',
                'bio' => 'No Bio',
                'date_of_birth' => $request->date_of_birth,
                'country' => $request->country,
                'city' => $request->city,
                'address' => $request->address,
                'CF' => $request->CF,
                'role' => 'admin',
                'credits' => '2147483647',
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'vat_number' => $request->vat_number,
                'email_address' => $request->email,
            ]);

            $admin->roles()->attach($roleId);

            return response()->json(['message' => 'User successfully registered!', 'user' => $admin], 201);
        } catch (\Exception $e) {
            Log::error('Unexpected error during user registration: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error.'], 500);
        }
    }

    // LOGIN 
    public function login(Request $request)
    {

        if ($request->has('email') && !$request->has('password')) { // Se la richiesta contiene solo l'email

            // Validazione delle credenziali e tentativo di login
            $validator = Validator::make($request->all(), [
                'email' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Only email required'], 422);
            }

            $hashGivenEmail = $request->email;

            $user = User::where('email', $hashGivenEmail)->first(); // Trovo lo user

            // Se l'utente non esiste entro nella tabella "trash_login"
            // Questa condizione vale anche per l'eventualità in cui l'utente
            // sia stato soft deletato, in quel caso la condizione if (!user)
            // restituirà true e quindi l'utente non verrà trovato
            if (!$user) {

                $fakeRandomSalt = bin2hex(random_bytes(16)); // Fake Random Salt da mandare al client

                return $fakeRandomSalt;
            }

            if ($user->banned) {
                return response()->json(['Banned' => 'User: ' . $user->name . ' is banned!'], 422);
            }

            $userCripted = DB::table('cripted')
                ->where('user_id', $user->id)
                ->first(); // Verifico se lo user è già presente nella tabella cripted

            // Se l'utente è presente verifico se l'account è bloccato
            if ($userCripted && $userCripted->locked) {

                return response()->json(['errore' => 'Account is locked! Login attempts Exceeded!'], 422);
            } else {

                if ($user && $user->email === $hashGivenEmail) { // Se user c'è e l'email del payload combacia con quella sul DB

                    // Trovo l'istanza di Cripted in base all'id utente e se esiste aggiorno stringa e tentativi
                    $userCripted = DB::table('cripted')->where('user_id', $user->id)->first();

                    if ($userCripted) {

                        $randomSalt = bin2hex(random_bytes(16)); // Random Salt da mandare al client

                        DB::table('cripted')
                            ->where('user_id', $user->id)
                            ->update([
                                'tries' => $userCripted->tries + 1,
                                'last_login_attempt' => now(),
                                'random_salt' => $randomSalt,
                            ]);
                    } else {

                        // Se non esiste un record per l'utente, ne creo uno nuovo
                        $randomSalt = bin2hex(random_bytes(16)); // Random Salt da mandare al client

                        DB::table('cripted')->insert([
                            'user_id' => $user->id,
                            'tries' => 1,
                            'last_login_attempt' => now(),
                            'random_salt' => $randomSalt,
                        ]);
                    }

                    // Verifico che il numero di tentativi non superi la configurazione
                    $configLoginTries = DB::table('configuration')->value('login_tries');

                    if ($userCripted && isset($userCripted->tries) && $userCripted->tries >= $configLoginTries) {

                        if (!$userCripted->locked) {

                            DB::table('cripted')
                                ->where('user_id', $user->id)
                                ->update(['locked' => true]);
                        }

                        return response()->json(['errore' => 'Account is Locked! Login attempts exceeded!'], 422);
                    }

                    // return $randomSalt; // Invio il random salt al client

                    $temp = $user->password . $randomSalt; // Invio temporaneo

                    return hash('sha256', $temp);
                } else {
                }
            }

            // USER E PASS ########################

        } else if ($request->has('email') && $request->has('password')) {

            // Validazione delle credenziali e tentativo di login
            $validator = Validator::make($request->all(), [
                'email' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validation failed', 'errors' => $validator->errors()], 422);
            }

            $hashGivenEmail = $request->email;
            $hashGivenPassword = $request->password;

            $user = User::where('email', $hashGivenEmail)->first(); // Trovo lo user

            if (!$user) { // Gestione FAKE LOGIN

                return response()->json(['message' => 'Invalid credentials! Fake Login']);
            }

            if ($user) { // Controllo se esiste lo lo user

                $userCripted = DB::table('cripted')
                    ->where('user_id', $user->id)
                    ->first();

                // Verifica se l'account è bloccato
                if ($userCripted && $userCripted->locked) {

                    return response()->json(['errore' => 'Account is locked! Login attempts Exceeded!'], 422);
                } else {

                    // Recupero il Random Salt dal database
                    $randomSaltObj = DB::table('cripted')->where('user_id', $user->id)->select('random_salt')->first();
                    $randomSalt = $randomSaltObj->random_salt ?? null;

                    if (!$randomSalt) { // Se il Random Salt non esiste
                        return response()->json(['errore' => 'Error!!']);
                    } else {

                        $passAndSalt = $user->password . $randomSalt;
                        $saltAndPassHash = hash('sha256', $passAndSalt);

                        if ($saltAndPassHash === $hashGivenPassword) {

                            $userCripted = DB::table('cripted')->where('user_id', $user->id)->first();

                            if ($userCripted) {

                                DB::table('cripted')
                                    ->where('user_id', $user->id)
                                    ->update([
                                        'tries' => 0,
                                        'last_login_attempt' => now(),
                                    ]);
                            }

                            $role = User::find($user->id)->role;

                            $tokenPayload = [
                                'iat' => now()->timestamp,
                                'role' => $role,
                            ];

                            $jwtToken = JWTAuth::fromUser($user, $tokenPayload);

                            // Salvo il token JWT sul DB
                            DB::table('cripted')->where('user_id', $user->id)->update(['jwt_token' => $jwtToken]);

                            // Invia il token JWT al client
                            return response()->json(['message' => 'Login Succesfull!', 'token' => $jwtToken]);
                        } else {
                            $userCripted = DB::table('cripted')->where('user_id', $user->id)->first();

                            if ($userCripted) {
                                DB::table('cripted')
                                    ->where('user_id', $user->id)
                                    ->update([
                                        'tries' => $userCripted->tries + 1,
                                        'last_login_attempt' => now(),
                                    ]);
                            } else {
                                // Se il record non esiste non c'è motivo di crearlo perchè vuol dire
                                // che non è stato fornito un valido username (o email)
                            }

                            // Verifico che il numero di tentativi non superi la configurazione
                            $configLoginTries = DB::table('configuration')->value('login_tries');

                            if ($userCripted && isset($userCripted->tries) && $userCripted->tries >= $configLoginTries) {

                                if (!$userCripted->locked) {

                                    DB::table('cripted')
                                        ->where('user_id', $user->id)
                                        ->update(['locked' => true]);
                                }

                                return response()->json(['errore' => 'Account is Locked! Login attempts exceeded!'], 422);
                            }
                        }
                    }
                }
            }
        }
    }
}
