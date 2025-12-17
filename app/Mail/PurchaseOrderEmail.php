<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body  = $body;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.purchase-order-email')
            ->with([
                'body' => $this->body
            ]);
    }
}