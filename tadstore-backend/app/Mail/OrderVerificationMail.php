<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $name;
    public $code;
    public $items;
    public $total;

    public function __construct($name, $code, $items = [], $total = 0)
    {
        $this->name = $name;
        $this->code = $code;
        $this->items = $items;
        $this->total = $total;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Código de verificación de pedido')
                    ->view('emails.order_verification');
    }

}
