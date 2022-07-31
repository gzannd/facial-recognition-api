<?php

namespace App\Events;

use App\Events\FaceDetectionDidFail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;

class FaceDetectionDidFailHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(EventLogService $logService)
    {
        $this->logService = $logService;
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
        $this->logService->LogApplicationEvent(LogLevel::Error, "Face detection failure", $event);
    }
}
