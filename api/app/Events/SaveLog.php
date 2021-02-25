<?php

namespace App\Events;

class SaveLog extends Event
{
    public $content;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }
}
