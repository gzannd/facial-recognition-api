<?php

namespace App\Providers;

use App\Providers\FaceDetectionDidComplete;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FaceDetectionDidCompleteHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\FaceDetectionDidComplete  $event
     * @return void
     */
    public function handle(FaceDetectionDidComplete $event)
    {
        
    }
}
