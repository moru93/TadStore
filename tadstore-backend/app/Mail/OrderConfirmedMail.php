<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("🛒 Pedido #{$this->order->id} confirmado - ¡Gracias por tu compra!")
                    ->markdown('emails.orders.confirmed')
                    ->with([
                        'order' => $this->order,
                    ]);
    }
}
