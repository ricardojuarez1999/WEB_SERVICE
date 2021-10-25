<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use ErrorException;
use Illuminate\Http\Request;
use Exception;
use Tymon\JWTAuth\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try{
                $tokenAuthorization = request()->bearerToken();
                $user = User::where('token',$tokenAuthorization)->get();
                $request->request->add(['useridentity' => $user[0]->useridentity]);
                if(!$user[0]){
                    return response()->json(['msg' =>  'No esta autenticado'],401);
                }
        }catch(Exception $e1){
            return response()->json(['err'=> $e1->getMessage(),'msg' =>  'No esta autenticado'],401);
        }
        return $next($request);
    }
}
