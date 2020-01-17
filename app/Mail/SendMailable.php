<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $nombre_archivo;
    public $imagenes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $nombre_archivo)
    {
        //
        //Log::info("entro al consutrctor");
        $this->data = $data;
        $this->nombre_archivo = $nombre_archivo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $urls = $this->data['images'];
        //dd($urls);
        return $this->from('info@ayaconsultora.com', 'INFO A&A')
            ->replyTo('nlopez@ayaconsultora.com')
            ->subject($this->data['subject'])
            ->view('email.registeredcount', compact('urls'))
            ->attach(public_path() . "/reportes/" . $this->nombre_archivo . '.pdf');

        return $this->view('view.name');
    }
}
