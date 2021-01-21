<?php

namespace App\Http\Controllers;

use App\Productor;
use App\ProductorVariedad;
use App\Region;
use App\Variedad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use yajra\Datatables\Datatables;

class ProductorVariedadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view("admin.productorVariedades.index");

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

        $regiones = Region::orderBy('region_nombre')->pluck('region_nombre', 'region_id');
        //$productores = Productor::all();
        $productores = [];
        $variedades = Variedad::all();
        $variedadesArray = array();
        foreach ($variedades as $item) {
            # code...
            $variedadesArray [] = [
                'id' => $item->variedad_id,
                'name' => $item->variedad_nombre
            ];
        }
        $variedades = $variedadesArray;
        return view('admin.productorVariedades.agregar', compact('regiones', 'productores', 'variedades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $variedadesObj = Variedad::all();
        $productor = Productor::find($id);
        foreach ($variedadesObj as $item) {
            # code...
            $variedades [] = [
                'id' => $item->variedad_id,
                'name' => $item->variedad_nombre
            ];
        }
        $disabled = false;

        return view('admin.productorVariedades.editar', compact( 'variedades','disabled','id','productor'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function setProductorVariedad(Request $request)
    {
        $region_id = $request->region_id;
        $productor_id = $request->productor_id;
        $variedad_id = $request->variedad_id;
        //return response()->json($request->productor_id);

        $arrayProveedores = array();
        $old = ProductorVariedad::whereProductorId($request->productor_id)->whereVariedadId($request->variedad_id)->first();
        if ($old) {
            return response()->json('');
        }
        $data = new ProductorVariedad();
        $data->productor_id = $productor_id;
        $data->variedad_id = $variedad_id;
        $prod = Productor::find($productor_id);
        $var = Variedad::find($variedad_id);
        $reg = Region::find($region_id);

        $data->save();

        $table = '<tr id="' . $productor_id . "-" . $variedad_id . '">' .
            '<td>' . ucfirst($reg->region_nombre) . '</td>' .
            '<td>' . ucwords($prod->productor_nombre) . '</td>' .
            '<td>' . ucwords($var->variedad_nombre) . '</td>' .
            '<td><a class="btn btn-danger boton" id="' . $productor_id . '-' . $variedad_id . '" >Eliminar</button></td>' .
            '</tr>';

        /*$productores  = Productor::where('region_id', $region_id)->get();
        //dd($productores);
        foreach($productores as $p){
            array_push($arrayProveedores, array( 'id' => $p->productor_id,
                'nombre' => $p->productor_nombre)
            );
        }*/
        return response()->json($table);
    }

    public function getVariedadByProductor(Request $request)
    {
        # code...
        $data = ProductorVariedad::whereProductorId($request->productor_id)->get();
        //Log::info($data);
        //dd($data);
        $table = '';
        $arrayVariedades = array();
        foreach ($data as $item) {
            //$prod = Productor::find($item->productor_id);
            $variedad = Variedad::find($item->variedad_id);
            array_push($arrayVariedades, array('id' => $variedad->variedad_id,
                    'nombre' => $variedad->variedad_nombre)
            );
        }
        return response()->json($arrayVariedades);
    }

    public function getProductorVariedadByProductor(Request $request)
    {
        # code...
        $data = ProductorVariedad::whereProductorId($request->productor_id)->get();
        //Log::info($data);
        //dd($data);
        $table = '';
        foreach ($data as $item) {
            $prod = Productor::find($item->productor_id);
            $variedad = Variedad::find($item->variedad_id);
            $table .= '<tr id="' . $item->productor_id . "-" . $item->variedad_id . '">' .
                '<td>' . ucfirst($prod->region->region_nombre) . '</td>' .
                '<td>' . ucwords($prod->productor_nombre) . '</td>' .
                '<td>' . ucwords($variedad->variedad_nombre) . '</td>' .
                '<td><a class="btn btn-danger boton" id="' . $item->productor_id . '-' . $item->variedad_id . '">Eliminar</button></td>' .
                '</tr>';
        }
        return response()->json($table);
    }


    public function productorVariedadDatetables()
    {

        $productores = Productor::select('productor_id', 'productor_nombre', 'region_id')->with('region')->get();
        return Datatables::of($productores)
            ->addColumn('action', function ($productores) {
                return '
                    <a href="' . route('productorVariedades.edit', $productores->productor_id) . '" class="btn btn-xs btn-warning"><i class="glyphicon glyphicon-edit"></i> Editar </a>
                    ';
            })
            ->make(true);
    }
}
