<?php

namespace App\Http\Controllers;

use App\Muestra;
use App\Productor;
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

        $to = Carbon::now();
        $from = Carbon::now('-24:00');
        $productors = Productor::all();

        $response = [];
        foreach ($productors as $productor) {
            $data = Muestra::whereBetween('created_at', [$from, $to])
                ->whereProductorId($productor->productor_id)
                ->whereIn('nota_id',[4,5])
                ->orderBy('nota_id','DESC')
                ->get();
            if(count($data) > 0){
                $response [] = $data;

            }
        }


        dd($response);





        dd("termino");

    }
}
