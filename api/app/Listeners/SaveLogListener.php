<?php

namespace App\Listeners;


use App\Events\SaveLog;
use App\Model\Log;
use Illuminate\Queue\Listener;

class SaveLogListener 
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
     * @param  SaveLog  $event
     * @return void
     */
    public function handle(SaveLog $event)
    {
      Log::create(['content' => $event->content]);
    }
}
