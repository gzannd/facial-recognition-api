<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Interfaces\IFacialRecognitionService;
use App\Http\Services\StorageService;
use App\Http\Services\EventLogService;
use App\Models\Image;
use App\Models\LogLevel;

class SendImageToDetectionService implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
    *The imgae to be processed
    **@var App\Models\Image
    */
    protected $imageData;
    protected $deviceId;
    protected $imageId;
    protected $logService;

    /**
     * Create a new job instance.
     *@param App\Models\Image $image
     * @return void
     */
    public function __construct($imageData, $imageId, $deviceId, $logService)
    {
      $this->imageData = $imageData;
      $this->deviceId = $deviceId;
      $this->imageId = $imageId;
      $this->logService = $logService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(IFacialRecognitionService $facialRecognitionService)
    {
        $this->logService->LogApplicationEvent(LogLevel::Debug, "SendImageToDetectionService.handle()");
        $facialRecognitionService->ProcessImage($this->imageData, $this->imageId, $this->deviceId);
    }
}
