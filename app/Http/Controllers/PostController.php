<?php

namespace App\Http\Controllers;

use App\Models\Amigo;
use App\Models\Image;
use App\Models\Post;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt',['except' => ['getPostId']]);
    }

    public function create(){
        try {
            $post = new Post();
            $post->userIdentityUser = request('useridentity');
            $post->description = request('description');
            if(request()->hasFile("image")){
                $path = request()->file('image')->store('images','s3');
                Storage::disk('s3')->setVisibility($path, 'public');
                $image = ImageController::createImage(basename($path),Storage::disk('s3')->url($path),2);
                $id = intval($image->id);
                $post->idImage = $id;
                $url = $image->url;
            }else{
                $url = null;
                $post->idImage = null;
            }
            $post->date = Carbon::now();
            $post->save();

            $aux = [
                'useridentity' => $post->userIdentityUser,
                'description'=> $post->description,
                'url' => $url,
                'date'=> $post->date,
                'id' => $post->id
            ];
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
                'data' => $aux,
                'msg' => 'Se proceso correctamente'
            ]
            ,200);
    }

    public function delete(){

    }

    public function getPostId($id){
        try{
            $post = Post::where('id', $id)->get();

            if($post[0]->idImage != null){
                $image = Image::where('id', $post[0]->idImage)->get();
                $image = $image[0]->url;
            }else{
                $image = null;
            }
            $aux = [
                'useridentity' => $post[0]->userIdentityUser,
                'description'=> $post[0]->description,
                'url' => $image,
                'date'=> $post[0]->date,
                'id' => $post[0]->id
            ];
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
                'data' => $aux,
                'msg' => 'Se proceso correctamente'
            ]
            ,200);
    }

    public function getPostsFriends(){
        try{
            $user1 = Amigo::select('amigos.user2 as user')->where('user1',request('useridentity'));
            $friends = Amigo::select('amigos.user1 as user')
                ->where('user2', request('useridentity'))
                ->union($user1)
                ->get();
            $friends[count($friends)] = ['user' => request('useridentity')];
            $posts = Post::select(
                'posts.id as idPost',
                'posts.userIdentityUser as useridentity',
                'images.url',
                'posts.description',
                'posts.date',
                )
                ->leftJoin('images', function($join) {
                $join->on('posts.idImage', '=', 'images.id');
                })
                ->whereIn('userIdentityUser',$friends)
                ->orderBy('date', 'DESC')->get();
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
                'data' => $posts,
                'msg' => 'Se proceso correctamente'
            ]
            ,200);
    }

    public function getPostsFriendsRange($start, $end){
        try{
            $user1 = Amigo::select('amigos.user2 as user')->where('user1',request('useridentity'));
            $friends = Amigo::select('amigos.user1 as user')
                ->where('user2', request('useridentity'))
                ->union($user1)
                ->get();
            $friends[count($friends)] = ['user' => request('useridentity')];
            $posts = Post::select(
                'posts.id as idPost',
                'posts.userIdentityUser as useridentity',
                'images.url',
                'posts.description',
                'posts.date',
            )
                ->leftJoin('images', function($join) {
                    $join->on('posts.idImage', '=', 'images.id');
                })
                ->whereIn('userIdentityUser',$friends)
                ->orderBy('date', 'DESC')
                ->limit($end)
                ->offset($start)
                ->get();
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
                'data' => $posts,
                'msg' => 'Se proceso correctamente'
            ]
            ,200);
    }
}
