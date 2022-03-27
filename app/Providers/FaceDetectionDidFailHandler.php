<?php

namespace App\Providers;

use App\Providers\FaceDetectionDidFail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FaceDetectionDidFailHandler
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
     * @param  \App\Providers\FaceDetectionDidFail  $event
     * @return void
     */
    public function handle(FaceDetectionDidFail $event)
    {
        //The facial detection event failed. Log the reason.
        
    }
}
