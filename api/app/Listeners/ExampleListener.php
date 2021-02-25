<?php

namespace App\Listeners;

use App\Events\ExampleEvent;
use App\Events\SaveLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExampleListener
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
     * @param  ExampleEvent  $event
     * @return void
     */
    public function handle(SaveLog $event)
    {
        var_dump($event);exit;
    }
}
