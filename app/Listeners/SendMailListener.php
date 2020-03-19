<?php

namespace App\Listeners;

use Mail;
use App\Events\SendMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendMail  $event
     * @return void
     */
    public function handle(SendMail $event)
    {
        $data =$event->data;
       
        Mail::send($data['template'],['data'=>$data], function($message) use ($data) {
         $message->to($data['toEmail'], $data['toName'])->subject
            ($data['subject']);
        });
    }
}
