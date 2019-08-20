<?php

namespace App\Http\Controllers;

use App\Defecto;
use App\Muestra;
use App\MuestraDefecto;
use App\Nota;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function index()
    {
        //TODO: filtrar por fechas?
        $notas = Nota::all();
        $result = array();

        $total = Muestra::all()->count();
        $pesoTotal = Muestra::all()->sum('muestra_peso');


        //dd($pesoTotal);
        foreach ($notas as $nota) {
            $result [$nota->nota_nombre]['nombre'] = $nota->nota_nombre;
            $result [$nota->nota_nombre]['color'] = $nota->color;
            $result [$nota->nota_nombre]['color_bg'] = $nota->color_bg;

            $result [$nota->nota_nombre]['tag'] = $nota->etiqueta_bootstrap;
            $result [$nota->nota_nombre]['cantidad'] = Muestra::where('nota_id', $nota->nota_id)->count();
            $result [$nota->nota_nombre]['porcentaje'] = round((Muestra::where('nota_id', $nota->nota_id)->count()) * 100 / $total, 2);
        }

        $defectos = Defecto::where('grupo_id','<>','null')->where('zona_id',1)->get();
        //dd($defectos);
        $data = array();
        foreach ($defectos as $defecto) {
            $data[$defecto->defecto_nombre]['nombre'] = $defecto->defecto_nombre;
            $data[$defecto->defecto_nombre]['promedioPorcentaje'] = round(MuestraDefecto::where('defecto_id', $defecto->defecto_id)->avg('muestra_defecto_calculo'),2);
            //$data[$defecto->defecto_nombre]['porcentaje'] =round(MuestraDefecto::where('defecto_id', $defecto->defecto_id)->sum('muestra_defecto_valor')*100/$pesoTotal,2);
            //$result [$nota->nota_nombre]['color'] = $nota->color;
            //$result [$nota->nota_nombre]['color_bg'] = $nota->color_bg;
        }
        //$data = sort($data);
        //dd($data);
        return view('admin.dashboard.dashboard', compact('result','data'));
    }
}
