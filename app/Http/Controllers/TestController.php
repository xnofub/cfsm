<?php

namespace App\Http\Controllers;

use App\Muestra;
use App\Services\ReportService;
use Carbon\Carbon;
use CpChart\Data;
use CpChart\Image;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //

    public function index()
    {
        $service = new ReportService();
        dd($service->get());
        dd("asd");

        $view = \View::make('pdf.reporte')->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);

        $pdf->save('reportes/reporte.pdf')->stream('reporte_test');
        dd("termino");

    }
}
