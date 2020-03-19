<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\RemoveAttachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RemoveEmailFile
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
     * @param  RemoveAttachment  $event
     * @return void
     */
    public function handle(RemoveAttachment $event)
    {
        if (Storage::disk('public')->exists('/temp/' . $event->data)) {
            Storage::disk('public')->delete('/temp/' . $event->data);
        }
    }
}
