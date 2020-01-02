<?php


namespace App\Services;


use App\Defecto;
use App\Mail\SendMailable;
use App\Muestra;
use App\MuestraDefecto;
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

                $flag = $this->generateReport($nombre_archivo, $nombre_fecha, $productor);


                Mail::to(['ricardoparramolina@gmail.com'])
                    ->send(new SendMailable(json_encode($muestras), $nombre_archivo));


                // Mail::to(['ricardoparramolina@gmail.com', 'nlopez@ayaconsultora.com'])
                //   ->send(new SendMailable(json_encode($muestras), $nombre_archivo));
            }
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
            $response [] = [
                'variedad' => [
                    'nombre' => (Variedad::find($item->variedad_id))->variedad_nombre,
                    'id' => $item->variedad_id
                ],
                'numero_pallet' => $item->lote_codigo,
                'nota' => [
                    'id' => $item->nota_id,
                    'nombre' => (Nota::find($item->nota_id))->nota_nombre,
                    'color' => (Nota::find($item->nota_id))->color,
                ],
            ];
        }


        $this->setGraph($response);

        return (Productor::find($productor->productor_id))->productor_nombre;
    }

    public function getDefectosByMuestra($muestraId)
    {

        return MuestraDefecto::whereMuestraId($muestraId)->get();
    }

    public function generateReport($nombre_reporte, $fecha, $productor)
    {
        error_reporting(E_ALL ^ E_DEPRECATED);
        $to = Carbon::now();
        $from = Carbon::now('-24:00');
        $productors = Productor::all();


        $response = false;
        $data = Muestra::whereBetween('created_at', [$from, $to])
            ->whereProductorId($productor->productor_id)
            ->whereIn('nota_id', [3, 4, 5])
            ->orderBy('nota_id', 'DESC')
            ->get();
        Log::info("adsdas");
        Log::info($data);
        if (count($data) > 0) {
            Log::info("entro");
            //$response [] = $data;

            foreach ($data as $item){
                $defectos = MuestraDefecto::selectRaw('`muestra_defecto_id`, MAX(`muestra_defecto_calculo`) as muestra_defecto_calculo,`defecto_id`')
                    ->where('defecto_id','!=',20)
                    ->whereMuestraId($item->muestra_id)
                    ->groupBy('muestra_defecto_id')
                    ->first();
                Log::info($defectos);
                $num_muestras = Muestra::where('lote_codigo',$item->lote_codigo)->count() ?? "";
                $response [] = [
                    'calificacion' => (Nota::find($item->nota_id))->nota_nombre,
                    'pallet' => $item->lote_codigo,
                    'defecto' => (Defecto::find($defectos->defecto_id))->defecto_nombre ?? "",
                    'porcentaje' => $defectos->muestra_defecto_calculo ?? "",
                    'num_muestras' => $num_muestras ?? ""
                ];
            }

        }


        Log::info($response);


        $view = \View::make('pdf.reporte', compact('fecha', 'productor', 'response'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->save(public_path() . '/reportes/' . $nombre_reporte . '.pdf')->stream('reporte_test');

        try {
            $view = \View::make('pdf.reporte', compact('fecha', 'productor'))->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->save(public_path() . '/reportes/' . $nombre_reporte . '.pdf')->stream('reporte_test');

            Log::info("termino lawea");
            return true;
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
            if ($item['nota']['id'] == 1) {
                $points[1]++;
                $points[1]++;
            }
            if ($item['nota']['id'] == 2) {
                $points[2]++;
            }
            if ($item['nota']['id'] == 3) {
                $points[3]++;
            }
            if ($item['nota']['id'] == 4) {
                $points[4]++;
            }
            if ($item['nota']['id'] == 5) {
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

        $pieChart->draw3DPie(370, 290, ["SecondPass" => false, "DrawLabels" => true, "WriteValues" => true, "Radius" => 310]);


        $ruta = public_path() . '/grafico.png';
        Log::info($ruta);
        chmod($ruta,0777);
        $image->render($ruta);

        $image->autoOutput($ruta);

    }

}
