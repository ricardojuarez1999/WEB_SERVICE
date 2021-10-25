<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use ErrorException;
use phpDocumentor\Reflection\Types\Object_;

class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt');
    }


    public function newChat()
    {
        try
        {
            $user1 = request('user1');
            $user2 = request('user2');
            $chat = Chat::where(['user1'=>$user1,'user2'=>$user2])->first();
            $chat2 = Chat::where(['user1'=>$user2,'user2'=>$user1])->first();
            if(is_null($chat) and is_null($chat2))
            {
                $chat = new Chat(request()->all());
                $chat->status = 0;
                $chat->date = date('Y-m-d h:i:s');
                $chat->save();
                return response()->json([
                    'msg' => 'Solicitud de chat creada',
                    'status' => '200'
                ]);

            }else
            {
                return response()->json([
                    'msg' => 'Ya existe un chat con esta persona',
                    'status' => '500'
                ]);
            }
        }catch(QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error en solicitud de chat',
                'status' => '500'
            ]);
        }
    }

    public function acceptChat($id)
    {
            try
            {
                $chat = Chat::find($id);
                if(is_null($chat))
                {
                    return response()->json([
                        'msg' => 'No se ha encontrado este chat',
                        'status' => '500'
                    ]);
                }
                $chat->update(['status'=>1]);
                return response()->json([
                    'msg' => 'Chat aceptado con exito',
                    'status' => '200'
                ]);
            }catch(QueryException $e)
            {
                return response()->json([
                    'err' => $e->getMessage(),
                    'msg' => 'Error en acpetacion de chat',
                    'status' => '500'
                ]);
            }
    }

    public function getAllChats()
    {
        try
        {
            $user = request('useridentity');
            $chats = Chat::Where('user1',$user)->orWhere('user2',$user)->orderBy('date', 'DESC')->get();
            return response()->json([
                'data'=>$chats,
                'msg' => 'Exito en peticion de chats',
                'status' => '200'
            ]);
        }catch(QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error en peticion de chats',
                'status' => '500'
            ]);
        }
    }

    public function getChatsRange($start,$end)
    {
        try
        {
            $user = request('useridentity');
            $chats = Chat::Where('user1',$user)->orWhere('user2',$user)
                ->orderBy('date', 'DESC')
                ->limit($end)
                ->offset($start)
                ->get();;
            return response()->json([
                'data'=>$chats,
                'msg' => 'Exito en peticion de chats',
                'status' => '200'
            ]);
        }catch(QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error en peticion de chats',
                'status' => '500'
            ]);
        }
    }

    public function getPendingChats()
    {
        try
        {
            $user = request('useridentity');
            $chats = Chat::Where(['user2'=>$user,'status'=>'0'])->orderBy('date', 'DESC')->get();
            return response()->json([
                'data'=>$chats,
                'msg' => 'Exito en peticion de chats',
                'status' => '200'
            ]);
        }catch(QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error en peticion de chats',
                'status' => '500'
            ]);
        }
    }

}
