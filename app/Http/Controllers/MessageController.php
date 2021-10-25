<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use ErrorException;
use phpDocumentor\Reflection\Types\Object_;
use App\Models\Chat;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt');
    }

    public function newMessage()
    {
        try
        {
            date_default_timezone_set('America/Guatemala');
            $user = request('useridentity');
            $datenow = date('Y-m-d h:i:s');
            $idchat = request('idChat');
            $chat = Chat::find($idchat);
            if(is_null($chat))
            {
                return response()->json([
                    'msg' => 'No se ha encontrado este chat',
                    'status' => '500'
                ]);
            }
            $chat->update(['date'=>$datenow]);
            $message = new Message(request()->all());
            $message->date = $datenow;
            $message->image = "N/A";
            $message->sendUser = $user;
            $message->save();
            return response()->json([
                'msg' => 'Exito en el envio de mensaje',
            ],200);
        }catch (QueryException $e)
        {
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Error al enviar mensaje',
            ],500);
        }
    }


    public function getMessageRange($id,$start, $end){
        try{
            $user = request('useridentity');
            $userAuth = Chat::Where('id',$id)
                ->where(function($query)  use ($user){
                    $query->Where('user1',$user)
                        ->orWhere('user2',$user);
                })
                ->first();
            if(is_null($userAuth))
            {
                return response()->json(
                    [
                        'msg' => 'No pertences a este chat',
                    ],500);
            }else
            {
                $Messages = Message::where('idChat', $id)
                    ->orderBy('id', 'DESC')
                    ->limit($end)
                    ->offset($start)
                    ->get();
                return response()->json(
                    [
                        'data' => $Messages,
                        'msg' => 'Se proceso correctamente',
                    ],200);
            }
        }catch(Exception $e){
            return response()->json(
                [
                    'err' => $e->getMessage(),
                    'msg' => 'Ocurrio un error, no se pudo procesar correctamente',
                ],500);
        }

    }

    public function getAllMessages($id){
        try{
            $user = request('useridentity');
            $userAuth = Chat::Where('id',$id)
                ->where(function($query)  use ($user){
                    $query->Where('user1',$user)
                        ->orWhere('user2',$user);
                })
                ->first();
            if(is_null($userAuth)){
                return response()->json(
                    [
                        'msg' => 'No pertences a este chat',
                    ],500);
            }else{
                $Messages = Message::where('idChat', $id)
                    ->orderBy('id', 'DESC')
                    ->get();
                return response()->json(
                    [
                        'data' => $Messages,
                        'msg' => 'Se proceso correctamente',
                    ],200);
            }
        }catch(Exception $e) {
            return response()->json(
                [
                    'err' => $e->getMessage(),
                    'msg' => 'Ocurrio un error, no se pudo procesar correctamente',
                ]
                , 500);
        }
    }


}
