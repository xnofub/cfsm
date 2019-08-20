<?php

namespace App\Http\Controllers;

use App\Defecto;
use App\Muestra;
use App\MuestraDefecto;
use App\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $defectos = Defecto::where('grupo_id', '<>', 'null')->where('zona_id', 1)->get();
        $data = array();
        foreach ($defectos as $defecto) {
            $data[$defecto->defecto_nombre]['nombre'] = $defecto->defecto_nombre;
            $data[$defecto->defecto_nombre]['promedioPorcentaje'] = round(MuestraDefecto::where('defecto_id', $defecto->defecto_id)->avg('muestra_defecto_calculo'), 2);
            $data [$defecto->defecto_nombre]['color'] = $defecto->defecto_color;
        }

        $cantidad = Muestra::limit(4)->count('productor_id');
        $cantidad = DB::select(DB::raw("SELECT 
            productor.productor_nombre,
            COUNT(muestra.productor_id) AS cantidad
            FROM 
            muestra
            inner join productor on muestra.productor_id = productor.productor_id
            GROUP BY
            productor.productor_nombre 
            ORDER BY 
            cantidad 
            DESC
            LIMIT 4
        "));
        $cantRes = array();
        foreach ($cantidad as $item) {
            $cantRes[] = ['nombre' => $item->productor_nombre, 'cantidad'=>$item->cantidad];

        }

        //dd($cantRes);

        return view('admin.dashboard.dashboard', compact('result', 'data','cantRes'));
    }
}
