<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Exception;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt',['except' => ['getBlogPostRangeUseridentity','getBlogPostAllUseridentity']]);
    }

    public function getBlogPostRange($start, $end){
        try{
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
                ->where('userIdentityUser',request('useridentity'))
                ->orderBy('posts.id', 'DESC')
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

    public function getBlogPostRangeUseridentity($id,$start, $end){
        try{
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
                ->where('userIdentityUser',$id)
                ->orderBy('posts.id', 'DESC')
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

    public function getBlogPostAll(){
        try{
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
                ->where('userIdentityUser',request('useridentity'))
                ->orderBy('posts.id','DESC')->get();
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

    public function getBlogPostAllUseridentity($id){
        try{
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
                ->where('userIdentityUser',$id)
                ->orderBy('posts.id','DESC')->get();
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
