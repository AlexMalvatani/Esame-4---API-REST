<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogOut
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica la presenza del token JWT nelle intestazioni della richiesta
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

            $user = DB::table('users')->where('id', $userCripted->user_id)->first();
            $name = $user->name;

            DB::table('cripted')->where('user_id', $userCripted->user_id)->update([
                'jwt_token' => null,
                'random_salt' => null,
                'tries' => 0,
            ]);


            return response()->json(['message' => 'Logout successful for user: ' . $name], 200);
        }
    }
}
