<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;

class ViewController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt',['except' => ['newView', 'getTopViews']]);
    }

   public function newView ($useridentity){
       try {
           $user = User::where('useridentity',$useridentity)->get();
           if(count($user) > 0){
               $aux = View::where([
                ['useridentityUser','=',$useridentity],
                ['date','=',Carbon::now()->format('Y-m-d')]
               ])->get();
               if(count($aux) == 0){
                   $view = new View();
                   $view->useridentityUser = $useridentity;
                   $view->count += 1;
                   $view->date = Carbon::now()->format('Y-m-d');
                   $view->save();
               }else {
                   $count = $aux[0]->count + 1;
                   View::where('id',$aux[0]->id)->update(['count' => $count]);
                   $aux[0]->count = $count;
                   $view = $aux[0];
               }
               return response()->json(
                   [
                       'data' => $view,
                       'msg' => 'Se proceso correctamente'
                   ]
                   ,200);
           }else {
               return response()->json(
                   [
                       'err' => 'Ocurrio un error nose pudo procesar correctamente',
                       'msg' => 'No existe el useridentity'
                   ]
                   ,302);
           }
       }catch(Exception $e){
           return response()->json(
               [
                   'err' => $e->getMessage(),
                   'msg' => 'Ocurrio un error nose pudo procesar correctamente'
               ]
               ,500);
       }
   }

   public function getTopViews(){
        try{
            $aux = View::where([
                ['date','=',Carbon::now()->format('Y-m-d')]
            ])->orderBy('count', 'DESC')->limit(5)->get();
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

}
