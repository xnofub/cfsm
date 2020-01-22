<?php


namespace App\Services;


use App\Defecto;
use App\Mail\SendMailable;
use App\MailingList;
use App\Muestra;
use App\MuestraDefecto;
use App\MuestraImagen;
use App\Nota;
use App\Productor;
use App\Variedad;
use Carbon\Carbon;
use CpChart\Chart\Pie;
use CpChart\Data;
use CpChart\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReportService
{
    public function get()
    {

        $to = Carbon::now();
        $nombre_fecha = $to->toDateString();

        $from = Carbon::now('-24:00');
        //$from = '2020-01-16 21:03:00';
        //$to = '2020-01-16 22:10:00';

        $productors = $this->getProductores();
        //return $this->generateReport();

        $muestras = [];
        $muestrasAll = [];

        foreach ($productors as $productor) {
            $productor_nombre = str_replace(" ", "_", ($productor->productor_nombre));

            $data = Muestra::whereBetween('created_at', [$from, $to])->whereProductorId($productor->productor_id)->get();
            if (count($data) > 0) {
                $muestras = [
                    'productor' => [
                        'id' => $productor->productor_id,
                        'name' => $productor->productor_nombre
                    ],
                    'muestras' => $this->getData($productor, $data)
                ];
                $muestrasAll [] = $muestras;
                //$productor_nombre = str_replace(" ", "_", ($productor->productor_nombre));
                $nombre_archivo = $nombre_fecha . "_" . $productor->productor_id;
                //Log::info($nombre_archivo);
                //dd($muestras);

                $flag = $this->generateReport($nombre_archivo, $nombre_fecha, $productor, $muestras);

                $datos_correo = [
                    'subject' => "Reporte " . $nombre_fecha . " - " . $productor->productor_nombre,
                    'body' => "Reporte diario de calidad",
                    'images' => $flag
                ];

                $mailingTo = ['ricardoparramolina@gmail.com', 'nlopez@ayaconsultora.com', 'rodrigor@cfsm.cl'];
                //$mailingTo = ['ricardoparramolina@gmail.com'];

                $mailingList = MailingList::whereProductorId($productor->productor_id)->first();
                $text = $mailingList->mailing_list ?? "";
                if ($text != "") {
                    $mailingTo = explode(";", $text);
                }
                //Log::info($text);
                //Log::info("ahora viene el otro");
                //Log::info(json_encode($mailingTo));

                //dd($mailingTo);
                //Log::info($datos_correo);


                Mail::to($mailingTo)
                    ->send(new SendMailable($datos_correo, $nombre_archivo));
                //dd("envio uno");

            }
            //dd("termino");
            $muestras = [];
        }
        return $muestrasAll;
    }

    public function getProductores()
    {
        return Productor::all();

    }

    public function getData($productor, $data)
    {
        $response = null;
        foreach ($data as $item) {
            $defectos = $this->getDefectosByMuestra($item->muestra_id);
            //dd($item->variedad_id);
            $response [] = [
                'variedad' => [
                    'nombre' => (Variedad::find($item->variedad_id))->variedad_nombre,
                    'id' => $item->variedad_id
                ],
                'numero_pallet' => $item->lote_codigo,
                'qr' => $item->muestra_qr,
                'nota' => [
                    'id' => $item->nota_id,
                    'nombre' => (Nota::find($item->nota_id))->nota_nombre,
                    'color' => (Nota::find($item->nota_id))->color,
                ],
            ];
        }
        //dd($response);
        $pallets = [];
        foreach ($response as $item) {
            $pallets[$item['numero_pallet']][] = $item;
        }

        $var = $this->getPalletCalification($pallets);
        //dd($var);


        $this->setGraph($var);

        return $var;

        return (Productor::find($productor->productor_id))->productor_nombre;
    }

    public function getPalletCalification($pallets)
    {
        $response = [];
        foreach ($pallets as $pallet) {
            $pallet['nota_final'] = $this->getIndividualPalletCalification($pallet);
            $response [] = $pallet;
        }

        return $response;
    }

    public function getIndividualPalletCalification($pallet)
    {

        $notas = [];
        $notasLetra['A'] = 0;
        $notasLetra['B'] = 0;
        $notasLetra['C'] = 0;
        $notasLetra['O'] = 0;
        $notasLetra['X'] = 0;
        foreach ($pallet as $key => $muestra) {
            $notas [] = $muestra['nota']['nombre'];
            $notasLetra[$muestra['nota']['nombre']]++;
        }
        $notasCount = count($pallet);
        if ($notasCount == 1) {
            $nota = $notas[0];
            return $nota;

        }
        if ($notasCount == 2) {
            if ($notasLetra['C'] == 2) {
                $nota = 'C';
                return $nota;
            } else {
                if ($notasLetra['O'] == 2) {
                    $nota = 'O';
                    return $nota;
                }
                if ($notasLetra['C'] > 0) {
                    $nota = 'C';
                    //return $nota;
                }
                if ($notasLetra['B'] > 0) {
                    $nota = 'B';
                    return $nota;
                }
                if ($notasLetra['A'] > 0) {
                    $nota = 'A';
                    return $nota;
                }
            }
        }

        if ($notasCount == 3) {
            if ($notasLetra['O'] >= 2) {
                $nota = 'O';
                return $nota;
            }
            if ($notasLetra['O'] == 1) {
                if (($notasLetra['A'] + $notasLetra['B'] + $notasLetra['C']) == 2) {
                    $nota = 'C';
                    return $nota;
                }
            }
            if ($notasLetra['A'] + $notasLetra['B'] == 2 && $notasLetra['C'] == 1) {
                $nota = 'B';
                return $nota;
            }
            if ($notasLetra['A'] + $notasLetra['B'] == 1 && $notasLetra['C'] == 2) {
                $nota = 'C';
                return $nota;
            }
        }
        return 'X';
        return $pallet;

    }

    public function getDefectosByMuestra($muestraId)
    {

        return MuestraDefecto::whereMuestraId($muestraId)->get();
    }

    public function generateReport($nombre_reporte, $fecha, $productor, $muestras)
    {
        error_reporting(E_ALL ^ E_DEPRECATED);
        $to = Carbon::now();
        $from = Carbon::now('-24:00');
        $productors = Productor::all();

        //$from = '2020-01-16 21:03:00';
        //$to = '2020-01-16 22:10:00';
        $images = [];


        $response = [];
        $data = Muestra::whereBetween('created_at', [$from, $to])
            ->whereProductorId($productor->productor_id)
            ->whereIn('nota_id', [1, 2, 3, 4])
            ->orderBy('nota_id', 'DESC')
            ->get();
        //Log::info("adsdas");
        //Log::info($data);
        $imagesShow = false;
        if (count($data) > 0) {
            foreach ($data as $item) {
                $imagenesMuestra = MuestraImagen::whereMuestraId($item->muestra_id)->get();
                if ($imagenesMuestra != null) {
                    foreach ($imagenesMuestra as $img) {
                        $imagesShow = false;
                        $images [] = [
                            'path' => base_path() . '/public/' . $img->muestra_imagen_ruta_corta,
                            'url' => $img->muestra_imagen_ruta,
                            'description' => $img->muestra_imagen_texto,
                            'pallet' => $item->lote_codigo

                        ];
                    }
                }

                $defectos = MuestraDefecto::selectRaw('`muestra_defecto_id`, MAX(`muestra_defecto_calculo`) as muestra_defecto_calculo,`defecto_id`')
                    ->where('defecto_id', '!=', 20)
                    ->whereMuestraId($item->muestra_id)
                    ->groupBy('muestra_defecto_id', 'defecto_id')
                    ->first();
                if ($defectos == null) {
                    $defectos = MuestraDefecto::selectRaw('`muestra_defecto_id`, MAX(`muestra_defecto_calculo`) as muestra_defecto_calculo,`defecto_id`')
                        ->whereMuestraId($item->muestra_id)
                        ->groupBy('muestra_defecto_id', 'defecto_id')
                        ->first();
                }
                if ($defectos == null) {
                    $def = "-";
                } else {
                    $def = (Defecto::find($defectos->defecto_id))->defecto_nombre;
                }
                //Log::info($defectos);
                $num_muestras = Muestra::where('lote_codigo', $item->lote_codigo)->count() ?? "";
                $response [] = [
                    'calificacion' => (Nota::find($item->nota_id))->nota_nombre,
                    'pallet' => $item->lote_codigo,
                    'calificacion_pallet' => 'X',
                    'qr' => $item->muestra_qr,
                    'variedad' => (Variedad::find($item->variedad_id))->variedad_nombre ?? "",
                    'defecto' => $def,
                    'porcentaje' => $defectos->muestra_defecto_calculo ?? "",
                    'num_muestras' => $num_muestras ?? ""
                ];
            }
        }
        //Log::info($images);


        $cantidad['A']['pallets'] = 0;
        $cantidad['A']['muestras'] = 0;
        $cantidad['B']['pallets'] = 0;
        $cantidad['B']['muestras'] = 0;
        $cantidad['C']['pallets'] = 0;
        $cantidad['C']['muestras'] = 0;
        $cantidad['O']['pallets'] = 0;
        $cantidad['O']['muestras'] = 0;
        $cantidad['X']['pallets'] = 0;
        $cantidad['X']['muestras'] = 0;
        $cantidadShow = false;
        foreach ($muestras['muestras'] as $key => $item) {


            $cantidadShow = true;
            //dd($item);
            $numMuestrasPallet = 0;
            foreach ($item as $key2 => $item2){
                if($key2 != 'nota_final'){
                    $numMuestrasPallet++;
                }
            }
            $cantidad[$item['nota_final']]['pallets']++;
            $cantidad[$item['nota_final']]['muestras'] = $numMuestrasPallet;
            $cantidad[$item['nota_final']]['label'] = $item['nota_final'];
        }

        //dd($cantidad);
        //Log::info(json_encode($cantidad));


        //Log::info($response);


        //$view = \View::make('pdf.reporte', compact('fecha', 'productor', 'response', 'cantidad', 'cantidadShow','images','imagesShow'))->render();
        //$pdf = \App::make('dompdf.wrapper');
        //$pdf->loadHTML($view);
        //$pdf->save(public_path() . '/reportes/' . $nombre_reporte . '.pdf')->stream('reporte_test');

        try {
            $view = \View::make('pdf.reporte', compact('fecha', 'productor', 'response', 'cantidad', 'cantidadShow', 'images', 'imagesShow'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->save(public_path() . '/reportes/' . $nombre_reporte . '.pdf')->stream('reporte_test');

            //Log::info("termino lawea");
            return $images;
        } catch (\Exception $e) {
            return false;
        }

    }

    public function setGraph($input)
    {


        $points[1] = 0;
        $points[2] = 0;
        $points[3] = 0;
        $points[4] = 0;
        $points[5] = 0;

        foreach ($input as $item) {
            if ($item['nota_final'] == 'A') {
                $points[1]++;
                $points[1]++;
            }
            if ($item['nota_final'] == 'B') {
                $points[2]++;
            }
            if ($item['nota_final'] == 'C') {
                $points[3]++;
            }
            if ($item['nota_final'] == 'O') {
                $points[4]++;
            }
            if ($item['nota_final'] == 'X') {
                $points[5]++;
            }
        }


        $puntos [] = $points[1];
        $puntos [] = $points[2];
        $puntos [] = $points[3];
        $puntos [] = $points[4];
        $puntos [] = $points[5];


        $data = new Data();
        $image = new Image(750, 600, $data, false);
        $data->addPoints($puntos, "ScoreA");
        $data->addPoints(["A", "B", "C", "O", "X"], "Labels");
        $data->setAbscissa("Labels");


        $pieChart = new Pie($image, $data);
        /* Define the slice color */
        $pieChart->setSliceColor(0, ["R" => 0, "G" => 0, "B" => 153]);
        $pieChart->setSliceColor(1, ["R" => 0, "G" => 102, "B" => 0]);
        $pieChart->setSliceColor(2, ["R" => 255, "G" => 255, "B" => 0]);
        $pieChart->setSliceColor(3, ["R" => 255, "G" => 0, "B" => 0]);
        $pieChart->setSliceColor(4, ["R" => 128, "G" => 128, "B" => 128]);


        /* Draw a simple pie chart */

        $pieChart->draw2DPie(370, 290, ["Border" => false, "SecondPass" => false, "DrawLabels" => true, "WriteValues" => true, "Radius" => 200, "ValuePosition" => PIE_VALUE_INSIDE]);


        $ruta = public_path() . '/grafico.png';
        //Log::info($ruta);
        chmod($ruta, 0777);
        $image->render($ruta);

        $image->autoOutput($ruta);

    }

}
