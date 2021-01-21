<?php

namespace App\Console\Commands;

use App\Categoria;
use App\Mail\SendMailable;
use App\Muestra;
use App\Productor;
use App\Services\ReportService;
use App\Variedad;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReportMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio automatizado de reportes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $service = new ReportService();
        $data = $service->get();
        /*$muestras = Muestra::limit(10)->orderBy('created_at')->get();
        $data = [];

        foreach ($muestras as $muestra) {
            $data [] = [
                'qr' => $muestra->muestra_qr,
                'Productor' => (Productor::find($muestra->productor_id))->productor_nombre,
                'Variedad' => (Variedad::find($muestra->variedad_id))->variedad_nombre,
                'Categoria' => (Categoria::find($muestra->categoria_id))->categoria_nombre
            ];
        }
        */



        //Mail::to(['ricardoparramolina@gmail.com','nlopez@ayaconsultora.com','iaraya@ayaconsultora.com'])
        //    ->send(new SendMailable( json_encode( $data) ));
        //Log::info('se supone que ya envio el mail');

    }
}
