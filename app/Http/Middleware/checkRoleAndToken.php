<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Models\User;
use App\Models\Cripted;


class checkRoleAndToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $allowedRoles = $roles;

        // Verifica la presenza del token nelle intestazioni della richiesta
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Token not found!!'], 401);
        } else {
            // Prendo il token
            $tokenTemp = $request->header('Authorization');
            $jwtToken = str_replace('Bearer ', '', $tokenTemp);

            // Cerco il match del token nella tabella "cripted"
            $userCripted = DB::table('cripted')->where('jwt_token', $jwtToken)->first();

            // Verifico che c'è un match
            if (!$userCripted) {
                return response()->json(['error' => 'Invalid Token'], 401);
            }

            // Ottengo il token JWT dalla tabella "cripted" usando l'id dell'utente
            $jwtToken = DB::table('cripted')->where('user_id', $userCripted->user_id)->value('jwt_token');

            JWTAuth::setToken($jwtToken);

            // Verifica la validità del token JWT
            $isValid = JWTAuth::check();

            if ($isValid) {
                // Controlla il ruolo dell'utente
                try {
                    $payload = JWTAuth::getPayload();
                    $userRole = $payload->get('role');

                    if (!in_array($userRole, $allowedRoles)) {
                        return response()->json(['error' => 'Unauthorized! Your role: ' . $userRole], 403);
                    }

                    return $next($request);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Error decoding token payload'], 500);
                }
            } else {
                try {
                    // Refresho il token
                    $newToken = JWTAuth::refresh($jwtToken);

                    // Ottengo il ruolo dell'utente dal database
                    $rolesTemp = DB::table('role_user')->where('user_id', $userCripted->user_id)->first();
                    $roleData = DB::table('roles')->where('id', $rolesTemp->role_id)->first();
                    $userRole = $roleData->name;

                    // Creo un nuovo payload con il ruolo
                    $tokenPayload = [
                        'iat' => now()->timestamp,
                        'role' => $userRole,
                    ];

                    $user = User::find($userCripted->user_id);
                    // Creo un nuovo token JWT con il payload personalizzato
                    $newToken = JWTAuth::fromUser($user, $tokenPayload);

                    // Aggiorno il token JWT sul DB solo se è stato refreshato
                    if ($newToken !== $jwtToken) {
                        $criptedModel = Cripted::where('user_id', $userCripted->user_id);

                        $criptedModel->update(['jwt_token' => $newToken]);
                    }

                    // Continuo con il nuovo token refreshato
                    $decodedToken = JWTAuth::setToken($newToken)->getPayload();

                    // Controllo il ruolo dell'utente con il nuovo token
                    $userRole = $decodedToken->get('role');

                    if (!in_array($userRole, $allowedRoles)) {
                        return response()->json(['error' => 'Unauthorized! Your role: ' . $userRole], 403);
                    }

                    return response()->json(['Token was expired! New Token: ' . $newToken]);
                    
                } catch (TokenInvalidException $e) {
                    return response()->json(['error' => 'Failed to refresh token', 'message' => $e->getMessage()], 401);
                } catch (\Exception $e) {
                    // Log dell'errore
                    Log::error($e);

                    return response()->json(['error' => 'Unexpected error while refreshing token', 'message' => $e->getMessage()], 500);
                }
            }
        }
    }
}
