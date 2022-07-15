<?php

namespace App\Listeners;

use App\Events\RawImageDataReceivedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

abstract class RawImageDataReceivedListener
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
     * @param  \App\Events\RawImageDataReceivedEvent  $event
     * @return void
     */
    abstract public function handle(RawImageDataReceivedEvent $event);
}
