<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF;
use Illuminate\Support\Facades\DB;

class PurchaseOrderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $body;
    public $data;
    public $pdfContent;

    public function __construct($title, $data, $pdfContent)
    {
        $this->title = $title;
        $this->data = $data;
        $this->pdfContent = $pdfContent;
    }
    
    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.purchase-order-email')
            ->with(['data' => $this->data])
            ->attachData(
                $this->pdfContent,
                'satin_alma_siparis_'.$this->data['EVRAKNO'].'.pdf',
                ['mime' => 'application/pdf']
            );
    }
    
}