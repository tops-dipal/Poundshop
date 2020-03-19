<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPO extends Mailable
{
    use Queueable, SerializesModels;

    public $purchaseOrder;
    public $pdfPath;
    public $tempFilename;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($purchaseOrder,$pdfPath,$tempFilename)
    {
        $this->purchaseOrder=$purchaseOrder;
        $this->pdfPath=$pdfPath;
        $this->tempFilename=$tempFilename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.po.sendpo',['purchaseOrder'=>$this->purchaseOrder])
                ->subject('Poundshop New Order #' . $this->purchaseOrder->po_number)
                ->attach($this->pdfPath,['as' => $this->purchaseOrder->po_number . '.pdf', 'mime' => 'application/pdf']);
        event(new \App\Events\RemoveAttachment($this->tempFilename));
    }
}
