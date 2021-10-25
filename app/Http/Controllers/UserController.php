<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;


class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt',['except' => ['getProfileUseridenity', 'search' ,'searchRange']]);
    }

    public function updateProfile()
    {
        try{
            if(request()->has('passwordOld') && request()->has('passwordNew')){
                $aux = User::where('useridentity',request('useridentity'))->get();
                if(Hash::check(request('passwordOld'), $aux[0]->password)) {
                    User::where('useridentity',request('useridentity'))->update(['password' => bcrypt(request('passwordNew'))]);
                }else {
                    return response()->json(
                        [
                            'err' => 'La contraseña anterior no coinciden',
                            'msg' => 'No se cambio la contraseña'
                        ]
                        ,302);
                }
            }
            if(request()->has('name')){
                User::where('useridentity',request('useridentity'))->update(['name' => request('name')]);
            }
            if(request()->has('nickname')){
                User::where('useridentity',request('useridentity'))->update(['nickname' => request('nickname')]);
            }
            if(request()->has('dob')){
                User::where('useridentity',request('useridentity'))->update(['dob' => request('dob')]);
            }
            if(request()->hasFile("image")){
                $path = request()->file('image')->store('images','s3');
                Storage::disk('s3')->setVisibility($path, 'public');
                $image = ImageController::createImage(basename($path),Storage::disk('s3')->url($path),1);
                $id = intval($image->id);
                User::where('useridentity',request('useridentity'))->update(['idImage' => $id]);
            }
            if(request()->has('description')){
                User::where('useridentity',request('useridentity'))->update(['description' => request('description')]);
            }
            $user = $this->getInfoUsers(request('useridentity'));
        }catch(Exception $e){
            return response()->json(
                [
                    'err' => $e->getMessage(),
                    'msg' => 'Ocurrio un error nose pudo procesar correctamente'
                ]
                ,500);
        }
        return response()->json(
            [
                'data' => $user,
                'msg' => 'Se proceso correctamente'
            ]
            ,200);
    }

    public function getProfile(){
        try {
            $user = $this->getInfoUsers(request('useridentity'));
        }catch(Exception $e) {
            return response()->json(['err' => $e->getMessage(),'msg' => 'Ocurrio un error'],500);
        }
        return response()->json(
            [
                'data' => $user,
                'msg' => 'Informacion del usuario'
            ]
            ,200);
    }

    public function getProfileUseridenity($id){
        try {
            $user = $this->getInfoUsers($id);
        }catch(Exception $e) {
            return response()->json(['err' => $e->getMessage(),'msg' => 'Ocurrio un error'],500);
        }
        return response()->json(
            [
                'data' => $user,
                'msg' => 'Informacion del usuario'
            ]
            ,200);
    }

    public function getInfoUsers($id): array
    {
        $aux = User::where('useridentity', $id)->get();

        if ($aux[0]->idImage != null) {
            $image = Image::where('id', $aux[0]->idImage)->get();
            $image = $image[0]->url;
        } else {
            $image = null;
        }
        return [
            'useridentity' => $aux[0]->useridentity,
            'email' => $aux[0]->email,
            'name' => $aux[0]->name,
            'nickname' => $aux[0]->nickname,
            'dob' => $aux[0]->dob,
            'url' => $image,
            'description' => $aux[0]->description,
        ];
    }

    public function search($data){
        try {
            $user = User::select(
                'users.id as idUser',
                'users.useridentity',
                'images.url',
                'users.name',
                'users.nickname',
                'users.email'
            )
                ->leftJoin('images', function($join) {
                    $join->on('users.idImage', '=', 'images.id');
                })
                ->where('useridentity','like','%'.$data.'%')
                ->orWhere('email','like','%'.$data.'%'.'@gmail.com')
                ->orWhere('name','like','%'.$data.'%')
                ->orWhere('nickname','like','%'.$data.'%')
                ->orderBy('users.id', 'DESC')
                ->get();
        }catch (Exception $e){
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Ocurrio un error'
            ],500);
        }

        return response()->json(
            [
                'data' => $user,
                'msg' => 'Informacion del usuarios'
            ]
            ,200);
    }

    public function searchRange($data, $start, $end){
        try {
            $user = User::select(
                'users.id as idUser',
                'users.useridentity',
                'images.url',
                'users.name',
                'users.nickname',
                'users.email'
            )
                ->leftJoin('images', function($join) {
                    $join->on('users.idImage', '=', 'images.id');
                })
                ->where('useridentity','like','%'.$data.'%')
                ->orWhere('email','like','%'.$data.'%'.'@gmail.com')
                ->orWhere('name','like','%'.$data.'%')
                ->orWhere('nickname','like','%'.$data.'%')
                ->limit($end)
                ->offset($start)
                ->orderBy('users.id', 'DESC')
                ->get();
        }catch (Exception $e){
            return response()->json([
                'err' => $e->getMessage(),
                'msg' => 'Ocurrio un error'
            ],500);
        }

        return response()->json(
            [
                'data' => $user,
                'msg' => 'Informacion del usuarios'
            ]
            ,200);
    }
}
