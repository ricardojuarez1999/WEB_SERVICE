<?php

namespace App\Http\Controllers;

use App\Models\Amigo;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use ErrorException;
use phpDocumentor\Reflection\Types\Object_;

class AmigoController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt');
    }


    public function newFriend()
    {
        try
        {
            $user1 = request('user1');
            $user2 = request('user2');
            $amigo = Amigo::where(['user1'=>$user1,'user2'=>$user2])->first();
            $amigo2 = Amigo::where(['user1'=>$user2,'user2'=>$user1])->first();
            if(is_null($amigo) and is_null($amigo2))
            {
                $amigo = new Amigo(request()->all());
                $amigo->status = 0;
                $amigo->save();
                return response()->json([
                    'msg' => 'Solicitud de amistad creada',
                    'status' => '200'
                ]);

            }else
            {
                return response()->json([
                    'msg' => 'Ya eres amigo de esta persona',
                    'status' => '500'
                ]);
            }
        }catch(QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error en solicitud de amistad',
                'status' => '500'
            ]);
        }
    }

    public function acceptFriend($id)
    {
        try
        {
            $amigo = Amigo::find($id);
            if(is_null($amigo))
            {
                return response()->json([
                    'msg' => 'No se encontro la solicitud de amistad',
                    'status' => '500'
                ]);
            }
            $amigo->update(['status'=>1]);
            return response()->json([
                'msg' => 'Solicitud aceptada con exito',
                'status' => '200'
            ]);
        }catch(QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error en acpetacion de solicitud',
                'status' => '500'
            ]);
        }
    }

    public function getAllFriends()
    {
        try
        {
            $user = request('useridentity');
            $amigos = Amigo::Where('user1',$user)->orWhere('user2',$user)->get();
            return response()->json([
                'data'=>$amigos,
                'msg' => 'Exito en peticion de amigos',
                'status' => '200'
            ]);
        }catch(QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error en peticion de amigos',
                'status' => '500'
            ]);
        }
    }

    public function getFriendsRange($start,$end)
    {
        try
        {
            $user = request('useridentity');
            $amigos = Amigo::Where('user1',$user)->orWhere('user2',$user)
                ->orderBy('id', 'DESC')
                ->limit($end)
                ->offset($start)
                ->get();;
            return response()->json([
                'data'=>$amigos,
                'msg' => 'Exito en peticion de amigos por rango',
                'status' => '200'
            ]);
        }catch(QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error en peticion de amigos por rango',
                'status' => '500'
            ]);
        }
    }

    public function getPendingFriends()
    {
        try
        {
            $user = request('useridentity');
            $amigos = Amigo::Where(['user2'=>$user,'status'=>'0'])->get();
            return response()->json([
                'data'=>$amigos,
                'msg' => 'Exito en peticion de solicitud de amigos',
                'status' => '200'
            ]);
        }catch(QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error en peticion de solicitud de amigos',
                'status' => '500'
            ]);
        }
    }

}
