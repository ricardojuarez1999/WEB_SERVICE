<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use ErrorException;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['login','register']]);
    }

    public function register(){
        try{
            $user = new User(request()->all());
            $user->password = bcrypt($user->password);
            $user->created_at = Carbon::now()->format('Y-m-d 00:00:00');
            $user->save();
            return response()->json([
                'msg' => 'Tu usuario se creo correctamente',
                'status' => '200'
            ]);
        }catch(QueryException $e){
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Uno de los datos ya existe',
            ],500);
        }
    }

    public function login()
    {
        try{
            $credentials = request(['email', 'password']);
            $user = User::where('email',request('email'))->get();
            if(Hash::check(request('password'), $user[0]->password)){
                if (! $token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                User::where('email',request('email'))->update(['token' => $token]);
                return $this->respondWithToken($token, $user);
            }else {
                return response()->json([
                    'err' => 'Unauthorized',
                    'msg' => 'Contraseña incorrecta'
                ],500);
            }
        }catch(Exception $e){
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Uno de los datos ya existe',
            ],500);
        }
    }

    public function logout()
    {
        try{
            $useridentity = request('useridentity');
            User::where('useridentity',$useridentity)->update(['token' => ""]);
        }catch(Exception $e){
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Ocurrio un error en el servidor'],
                500
            );
        }
        //auth()->logout();
        return response()->json(['msg' =>  'Se cerro sesion correctamente']);
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60000,
            'useridentity' => $user[0]->useridentity,
            'is_admin'=> $user[0]->is_admin
        ]);
    }

    public function isAdministrador(){
        try{
            $useridentity = \request('useridentity');
            $user = User::where('useridentity',$useridentity)->get();

            if(count($user) == 0){
                return response()->json([
                    'err' => 'No encontro el usuario',
                    'msg' => 'Ocurrio un error en el servidor'],
                    302
                );
            }
        }catch(Exception $e){
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Ocurrio un error en el servidor'],
                502
            );
        }
        return response()->json([
            'useridentity' => $user[0]->useridentity,
            'is_admin'=> $user[0]->is_admin
        ]);
    }
}
