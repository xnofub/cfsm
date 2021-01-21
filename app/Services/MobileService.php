<?php


namespace App\Services;


use App\Apariencia;
use App\Calibre;
use App\Categoria;
use App\Concepto;
use App\Defecto;
use App\Embalaje;
use App\Etiqueta;
use App\Grupo;
use App\Muestra;
use App\MuestraDefecto;
use App\Nota;
use App\Productor;
use App\Region;
use App\ToleranciaGrupo;
use App\User;
use App\Variedad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MobileService
{
    public function sync(Request $request)
    {
        $data['region'] = Region::all();
        $data['productor'] = Productor::all();
        $data['variedad'] = Variedad::all();
        $data['categoria'] = Categoria::all();
        $data['embalaje'] = Embalaje::all();
        $data['etiqueta'] = Etiqueta::all();
        $data['apariencia'] = Apariencia::all();
        $data['grupo'] = Grupo::all();
        $data['defecto'] = Defecto::all();
        $data['tolerancias'] = ToleranciaGrupo::all();
        $data['concepto'] = Concepto::all();
        $data['nota'] = Nota::all();
        $data['calibre'] = Calibre::all();

        $muestra = $request->data;
        foreach ($muestra as $m) {
            try {
                $muestraInput = [
                    'muestra_fecha' => $m['fecha'],
                    'muestra_qr' => $m['qr'],
                    'region_id' => (Region::whereRegionNombre($m['region'])->first())->region_id,
                    'productor_id' => (Productor::whereProductorNombre($m['productor'])->first())->productor_id,
                    'especie_id' => 1,
                    'variedad_id' => (Variedad::whereVariedadNombre($m['variedad'])->first())->variedad_id,
                    'calibre_id' => 11,
                    'categoria_id' => (Categoria::whereCategoriaNombre($m['categoria'])->first())->categoria_id,
                    'embalaje_id' => (Embalaje::whereEmbalajeNombre($m['embalaje'])->first())->embalaje_id,
                    'etiqueta_id' => (Etiqueta::whereEtiquetaNombre($m['etiqueta'])->first())->etiqueta_id,
                    'nota_id' => (Nota::whereNotaNombre($m['nota'])->first())->nota_id,
                    'lote_codigo' => $m['pallet'],
                    'muestra_peso' => $m['peso'],
                    'estado_muestra_id' => 3,
                    'apariencia_id' => (Apariencia::whereAparienciaNombre($m['apariencia'])->first())->apariencia_id,
                    'user_id' => $m['user_id'],
                    'muestra_bolsas' => $m['num_bolsas'],
                    'muestra_racimos' => $m['num_racimos'],
                    'muestra_brix' => $m['brix']
                ];
                $created = Muestra::create($muestraInput);
                //Log::info($created->muestra_id);
                for ($i = 1; $i <= 20; $i++) {
                    if ((Defecto::whereDefectoNombre($m['defecto_' . $i])->first()) == null) {
                        continue;
                    }
                    $input['muestra_id'] = $created->muestra_id;
                    $input['defecto_id'] = (Defecto::whereDefectoNombre($m['defecto_' . $i])->first())->defecto_id;
                    $input['muestra_defecto_valor'] = $m['valor_' . $i];
                    $input['nota_id'] = (Nota::first())->id;
                    $input['muestra_defecto_calculo'] = $m['suma_' . $i];
                    $muestraDefecto = MuestraDefecto::create($input);
                    //Log::info($muestraDefecto->muestra_defecto_id);
                }

            } catch (\Exception $e) {

            }
            //dd($created);
            //dd($muestraInput);
            //dd($m['region']);
            //$fecha = $m['fecha']
        }


        //dd($muestra);


        return $data;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ];
        }

        return '';
    }

}
