<?php

namespace App\Http\Controllers\Mobile;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Productor;
use App\Variedad;
use App\Region;
use App\Especie;
use App\Etiqueta;
use App\Calibre;
use App\Categoria;
use App\Embalaje;
use App\EstadoMuestra;
use App\MuestraDefecto;
use App\Nota;
use App\Defecto;
use App\Tolerancia;
use App\ToleranciaGrupo;
use App\ZonaDefecto;
use App\Apariencia;
use App\Grupo;
use App\Muestra;
use App\Concepto;
use Carbon\Carbon;
use App\Pallet;
use DB;

class MobileApiController extends Controller
{

    protected $muestras;
    
    public function __construct () {}


    /**
     * 
     * FUNCIONES PARA LA APLICACION 
     * 
     */

     public function pruebapancho () {

        $pallets = Pallet::all();


        dd($pallets);

     }





    public function muestras (Request $request) {
        $this->muestras = Muestra::with([
            'region', 
            'productor', 
            'especie', 
            'variedad', 
            'calibre', 
            'categoria', 
            'embalaje', 
            'etiqueta', 
            'nota', 
            'estado_muestra', 
            'apariencia'
        ])->get();
        return response()->json([
            'status' => 200,
            'msj' => 'ok',
            'muestras' => $this->muestras
        ]);
    }

    public function obtenerCalificacionMuestra (Request $request) {

        $muestra_id = $request->muestra_id;
        #Carga la muestra segun el id
        $muestra = Muestra::find($muestra_id);
        #Busca los conceptos, calidad y condicion
        $conceptos = Concepto::all();
        #Busca todas las apariencias
        $apariencias = Apariencia::all();
        #Busca grupos
        $grupos = Grupo::all();

        # De cada grupo y zona te da el acumulado por defecto
        $statement = "SELECT
        z.`zona_id`
        ,z.zona_nombre
        , c.`concepto_id`
        , c.`concepto_nombre`
        , g.`grupo_id`
        , g.`grupo_nombre`
        ,SUM(df.`muestra_defecto_calculo`) AS total_grupo
        FROM  muestra_defecto df
        inner join defecto d ON df.`defecto_id` = d.`defecto_id` and df.muestra_id = $muestra_id
        INNER JOIN zona_defecto z ON z.zona_id = d.`zona_id`
        INNER JOIN concepto c ON c.`concepto_id` = d.`concepto_id`
        INNER JOIN grupo g ON g.`grupo_id` = d.grupo_id
        INNER JOIN nota n ON n.`nota_id` = df.`nota_id`
        INNER JOIN muestra m ON m.`muestra_id` = $muestra_id
        GROUP BY z.zona_nombre
        , z.`zona_id`
        , c.`concepto_id`
        , c.`concepto_nombre`
        , g.`grupo_id`
        , g.`grupo_nombre`
        ";
        #INICIALIZA LAS NOTAS GENERALES
        $nota_max = 1; # en caso que no venga nada
        $arrayCalidad = array();
        array_push($arrayCalidad, 1);
        $arrayCondicion = array();
        array_push($arrayCondicion, 1);


        $nota_calidad = max($arrayCalidad);
        $nota_condicion = max($arrayCondicion);
        $grupos_totales = DB::select(DB::raw($statement));

        # por cada grupo total hace un foreach
        foreach($grupos_totales as $g){
            //echo $g->concepto_id ." - ".$g->grupo_id." - ".$g->total_grupo;
            //echo "<br>";
            /*$query = "select *
            from tolerancia_grupo
            where grupo_id = $g->grupo_id
            and categoria_id = $muestra->categoria_id
            and tolerancia_grupo_desde <=  $g->total_grupo
            and tolerancia_grupo_hasta >=  $g->total_grupo  ";

            $result = DB::select(DB::raw($query));
            dd($query);*/

            # Calcula tolerancia por el acumulado del grupo
            # Todos los defectos 
            $result = ToleranciaGrupo::where('grupo_id',$g->grupo_id)
            ->where('categoria_id',$muestra->categoria_id)
            ->where('tolerancia_grupo_desde','<=',$g->total_grupo)
            ->where('tolerancia_grupo_hasta','>=',$g->total_grupo)
            ->first();


            # luego eesto da una nota_id
            if(isset($result->nota_id)){
                # Valida si es calidad7condicion y si aplica
                    if($g->concepto_id == 1 ){
                        #CONCEPTO 1 CALIDAD
                        array_push($arrayCalidad, $result->nota_id);
                    }else{
                        #CONCEPTO 2 CONDICION
                        array_push($arrayCondicion, $result->nota_id);
                    }
            }else{
            if($g->concepto_id == 1 ){
                #CONCEPTO 1 CALIDAD
                array_push($arrayCalidad, 4);
            }else{
                #CONCEPTO 2 CONDICION

                array_push($arrayCondicion, 4);
            }
            }

        }
        $nota_max_calidad = max($arrayCalidad);
        $nota_calidad = Nota::find($nota_max_calidad);
        $nota_calidad_nombre = $nota_calidad->nota_nombre;

        $nota_max_condicion = max($arrayCondicion);
        $nota_condicion = Nota::find($nota_max_condicion);
        $nota_condicion_nombre = $nota_condicion->nota_nombre;

        if($nota_max_calidad >= $nota_max_condicion){
            $nota = Nota::find($nota_max_calidad);
        }else{
            $nota = Nota::find($nota_max_condicion);
        }

        if($nota->nota_id < $muestra->nota_id){
            $nota = Nota::find($muestra->nota_id);
        }
        $muestras_defecto = MuestraDefecto::where('muestra_id',$muestra_id)->get();
        #dd($muestra);
        return response()->json([
            'grupos_totales' => $grupos_totales,
            'grupos' => $grupos,
            'conceptos' => $conceptos,
            'muestra' => $muestra,
            'nota' => $nota,
            'muestras_defecto' => $muestras_defecto,
            'nota_calidad_nombre' => $nota_calidad_nombre,
            'nota_condicion_nombre' => $nota_condicion_nombre,
            'apariencias' => $apariencias
        ]);
    }

    public function obtenerMuestra (Request $request) {

        #$rules = config('validations.obtenerMuestra.rules');
        #$messages = config('validations.obtenerMuestra.messages');

        #$this->validate($request, $rules, $messages);

        $this->muestra = Muestra::with([
            'region' => function ($region) {
                $region->select('region_id', 'region_nombre');
            },
            'productor' => function ($productor) {
                $productor->select('productor_id','productor_nombre');
            },
            'especie' => function ($especie) {
                $especie->select('especie_id','especie_nombre');
            },
            'variedad' => function ($variedad) {
                $variedad->select('variedad_id','variedad_nombre');
            },
            'calibre' => function ($calibre) {
                $calibre->select('calibre_id','calibre_nombre');
            },
            'embalaje' => function ($embalaje) {
                $embalaje->select('embalaje_id','embalaje_nombre');
            },
            'etiqueta' => function ($etiqueta) {
                $etiqueta->select('etiqueta_id','etiqueta_nombre');
            },
            'muestras_defectos.nota',
            'muestras_defectos.defecto',
            'muestras_defectos.muestra',
            'estado_muestra'
        ])->findOrFail($request->muestra_id);

        return response()->json([
            'status' => 200,
            'msj' => 'ok',
            'muestra' => $this->muestra
        ]);
    }

    public function obtenerMuestraPorQR (Request $request) {

        #$rules = config('validations.obtenerMuestraPorQR.rules');
        #$messages = config('validations.obtenerMuestraPorQR.messages');

        #$this->validate($request, $rules, $messages);
        

        $this->muestra = Muestra::with([
            'region' => function ($region) {
                $region->select('region_id', 'region_nombre');
            },
            'productor' => function ($productor) {
                $productor->select('productor_id','productor_nombre');
            },
            'especie' => function ($especie) {
                $especie->select('especie_id','especie_nombre');
            },
            'variedad' => function ($variedad) {
                $variedad->select('variedad_id','variedad_nombre');
            },
            'calibre' => function ($calibre) {
                $calibre->select('calibre_id','calibre_nombre');
            },
            'embalaje' => function ($embalaje) {
                $embalaje->select('embalaje_id','embalaje_nombre');
            },
            'etiqueta' => function ($etiqueta) {
                $etiqueta->select('etiqueta_id','etiqueta_nombre');
            },
            'estado_muestra'
        ])->where('muestra_qr', $request['muestra_qr'])->first();

        return response()->json([
            'status' => 200,
            'msj' => 'ok',
            'muestra' => $this->muestra
        ]);
    }


    public function guardarMuestra (Request $request) {

        $rules = config('validations.guardarMuestra.rules');
        $messages = config('validations.guardarMuestra.messages');

        $this->validate($request, $rules, $messages);

        $this->muestra = new Muestra(['muestra_qr' => $request->muestra_qr]);
        $this->muestra->save();

        return response()->json([
            'status' => 200,
            'msj' => 'ok',
            'muestra' => $this->muestra
        ]);

    }




    public function actualizarMuestra (Request $request) {

        #$rules = config('validations.actualizarMuestra.rules');
        #$messages = config('validations.actualizarMuestra.messages');

        $this->apariencia = Apariencia::find($request->apariencia_id);

        $this->muestra = Muestra::find($request->muestra_id);

        $this->muestra->muestra_qr = $request->muestra_qr;
        $this->muestra->region_id = $request->region_id;
        $this->muestra->productor_id = $request->productor_id;
        $this->muestra->especie_id = $request->especie_id;
        $this->muestra->variedad_id = $request->variedad_id;
        $this->muestra->calibre_id = $request->calibre_id;
        $this->muestra->categoria_id = $request->categoria_id;
        $this->muestra->embalaje_id = $request->embalaje_id;
        $this->muestra->apariencia_id = $request->apariencia_id;
        $this->muestra->etiqueta_id = $request->etiqueta_id;
        $this->muestra->muestra_peso = $request->muestra_peso;
        $this->muestra->muestra_fecha = Carbon::parse($request->muestra_fecha)->toDateTimeString();
        $this->muestra->nota_id = $this->apariencia->nota_id; //PROCESO
        $this->muestra->estado_muestra_id = 1;
        $this->muestra->muestra_bolsas = $request->muestra_bolsas;
        $this->muestra->muestra_racimos = $request->muestra_racimos;
        $this->muestra->muestra_brix = $request->muestra_brix;
        $this->muestra->muestra_desgrane = $request->muestra_desgrane;
        $this->muestra->lote_codigo = $request->lote_codigo;
        $this->muestra->estado_muestra_id = $request->estado_muestra_id;
        $this->muestra->save();

        return response()->json([
            'status' => 200,
            'msj' => 'ok',
            'muestra' => $this->muestra
        ]);

    }




    public function eliminarMuestra (Request $request) {

        #$rules = config('validations.eliminarMuestra.rules');
        #$messages = config('validations.eliminarMuestra.messages');

        #$this->validate($request, $rules, $messages);

        $this->muestra = Muestra::findOrFail($request->muestra_id);
        $this->muestra->delete();

        return response()->json([
            'status' => 200,
            'msj' => 'ok',
        ]);

    }

    public function anularMuestra (Request $request) {

        $estado_anulado = 4;
        $this->muestra = Muestra::findOrFail($request->muestra_id);
        $this->muestra->estado_muestra_id = $estado_anulado;
        $this->muestra->save();

        return response()->json([
            'status' => 200,
            'msj' => 'ok',
        ]);
    }




    public function getDataControlCalidad () {
        $productores = Productor::all();
        $regiones = Region::all();
        $especies = Especie::all();
        $variedades = Variedad::all();
        $etiquetas = Etiqueta::all();
        $calibres = Calibre::all();
        $categorias = Categoria::all();
        $embalajes = Embalaje::all();
        $estados_muestras = EstadoMuestra::all();
        $notas = Nota::all();
        $defectos = Defecto::all();
        $tolerancias = Tolerancia::all();
        $apariencias = Apariencia::all();
        $grupos = Grupo::all();
        $zonas_defectos = ZonaDefecto::all();
        return response()->json([
            'status' => 200,
            'msg' => 'ok',
            'productores' => $productores,
            'regiones' => $regiones,
            'especies' => $especies,
            'variedades' => $variedades,
            'etiquetas' => $etiquetas,
            'calibres' => $calibres,
            'categorias' => $categorias,
            'embalajes' => $embalajes,
            'estados_muestras' => $estados_muestras,
            'notas' => $notas,
            'defectos' => $defectos,
            'tolerancias' => $tolerancias,
            'apariencias' => $apariencias,
            'grupos' => $grupos,
            'zonas_defectos' => $zonas_defectos
        ]);

    }

    public function getDataControlCalidadCompactada () {
        $productores = Productor::all();
        $regiones = Region::all();
        $especies = Especie::all();
        $variedades = Variedad::all();
        $etiquetas = Etiqueta::all();
        $calibres = Calibre::all();
        $categorias = Categoria::all();
        $embalajes = Embalaje::all();
        $estados_muestras = EstadoMuestra::all();
        $notas = Nota::all();
        $defectos = Defecto::all();
        $tolerancias = Tolerancia::all();
        $apariencias = Apariencia::all();
        $grupos = Grupo::all();
        $zonas_defectos = ZonaDefecto::all();
        return response()->json([
            'status' => 200,
            'msg' => 'ok',
            'dataControlCalidad' => [
                'productores' => $productores,
                'regiones' => $regiones,
                'especies' => $especies,
                'variedades' => $variedades,
                'etiquetas' => $etiquetas,
                'calibres' => $calibres,
                'categorias' => $categorias,
                'embalajes' => $embalajes,
                'estados_muestras' => $estados_muestras,
                'notas' => $notas,
                'defectos' => $defectos,
                'tolerancias' => $tolerancias,
                'apariencias' => $apariencias,
                'grupos' => $grupos,
                'zonas_defectos' => $zonas_defectos
            ],

        ]);

    }








    /**
     * 
     * PARA GUARDAR DEFECTOS EN LA MUESTRA
     * 
     */

    public function guardarDefectoMuestra (Request $request) {

        #return dd($request->all());
        $request = json_decode(json_encode($request->all()));
        #dd($request);



        $muestra = Muestra::find($request->muestra_id);
        $defecto_id = $request->defecto_id;
        $defecto = Defecto::find($defecto_id);
        $muestra_defecto_valor = $request->valor_defecto;

        if($defecto->zona_id == 1 ){
            #CALCULO POR %
            $calculado = round((($muestra_defecto_valor*100)/$muestra->muestra_peso),2);
            $tolerancia  = Tolerancia::where('defecto_id',$defecto_id)
            ->where('tolerancia_desde','<=',$calculado)
            ->where('tolerancia_hasta','>=',$calculado)
            ->first();
            //NOTA $tolerancia->nota->nota_nombre
            //NOTA $tolerancia->nota->nota_id
            if(isset($tolerancia->nota->nota_id)){
                $nota_id = $tolerancia->nota->nota_id;
            }else{
                $nota_id = 5;
            }
            $nota = Nota::find($nota_id);
        }else{
            #CALCULO POR NUMERO
            $calculado = $muestra_defecto_valor;
            $nota_id = 5;
            $nota = Nota::find($nota_id);
            $muestra_defecto_valor=$muestra_defecto_valor;
        }
        //return response()->json(1);
        #print_r($tolerancia->nota->nota_nombre);

        $muestra_defecto = New MuestraDefecto();
        $muestra_defecto->muestra_id = $request->muestra_id;
        $muestra_defecto->defecto_id = $request->defecto_id;
        $muestra_defecto->muestra_defecto_valor = $request->valor_defecto;
        $muestra_defecto->nota_id = $nota->nota_id;
        $muestra_defecto->muestra_defecto_calculo = $calculado;
        $muestra_defecto->save();
        
        return response()->json([
            'status' => 200,
            'msg' => 'ok',
            'defecto' => $muestra_defecto
        ]);



    }















    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
