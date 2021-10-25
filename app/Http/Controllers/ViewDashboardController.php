<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\View_dashboard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;

class ViewDashboardController extends Controller
{
    public function validatePage($page){
        $page = strtoupper($page);
        $pages = ['INICIO','AMIGOS','BUSQUEDA','PERFIL','MENSAJES','CONFIGURACION','LOGIN','DASHBOARD','PAGINA'];
        $aux = array_search($page, $pages);

        if ($page == $pages[0]){
            $aux = true;
        }else if($aux > 0){
            $aux = true;
        }else{
            $aux = false;
        }
        return $aux;
    }

    public function newViewPage($page){
        try {
            if($this->validatePage($page)){
                $page = strtoupper($page);
                $aux = View_dashboard::where('namePage', $page)->get();
                if(count($aux) == 0){
                    $aux = new View_dashboard();
                    $aux->namePage = strtoupper($page);
                    $aux->total_views += 1;
                    $aux->real_views += 1;
                    $aux->date = Carbon::now()->format('Y-m-d');
                    $aux->save();

                    return response()->json(
                        [
                            'data' => $aux,
                            'msg' => 'Se proceso correctamente'
                        ]
                        ,200);
                }else {
                    $auxDate = Carbon::now()->format('Y-m-d 00:00:00');
                    if($aux[0]->date == $auxDate){
                        $aux[0]->total_views += 1;
                        $aux[0]->real_views +=1;
                        View_dashboard::where('namePage',$page)->update(['total_views' => $aux[0]->total_views, 'real_views' => $aux[0]->real_views]);
                    }else{
                        $aux[0]->total_views += 1;
                        $aux[0]->real_views = 0;
                        $aux[0]->date = Carbon::now()->format('Y-m-d 00:00:00');
                        View_dashboard::where('namePage',$page)->update(['total_views' => $aux[0]->total_views, 'real_views' => $aux[0]->real_views,'date'=>$aux[0]->date]);
                    }
                    return response()->json(
                        [
                            'data' => $aux[0],
                            'msg' => 'Se proceso correctamente'
                        ]
                        ,200);
                }
            }else {
                return response()->json(
                    [
                        'err' => 'La pagina proporcionada no existe',
                        'msg' => 'Ocurrio un error no se pudo procesar correctamente'
                    ]
                    ,500);
            }
        }catch(Exception $e){
            return response()->json(
                [
                    'err' => $e->getMessage(),
                    'msg' => 'Ocurrio un error no se pudo procesar correctamente'
                ]
                ,500);
        }
    }

    public function getViewPages(){
        try{
            $viewsPages = View_dashboard::all();
            $total_views = View_dashboard::all()->sum('total_views');
            $total_real_views = View_dashboard::all()->sum('real_views');

            $total_views_login = View_dashboard::where('namePage', 'LOGIN')->get();
            $total_register = User::all()->count();
            $tasa_conversion = ($total_register * 100) / $total_views_login[0]->total_views ;
        }catch(Exception $e){
            return response()->json(
                [
                    'err' => $e->getMessage(),
                    'msg' => 'Ocurrio un error no se pudo procesar correctamente'
                ]
                ,500);
        }
        return response()->json(
            [
                'data' => [
                    'views_pages' => $viewsPages,
                    'total_views' => $total_views,
                    'total_real_views' => $total_real_views,
                    'total_register' => $total_register,
                    'tasa_conversion' => $tasa_conversion,
                ],
                'msg' => 'Se proceso correctamente'
            ]
            ,200);
    }
}
