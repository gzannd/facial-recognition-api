<?php

namespace App\Events;

use App\Events\FaceDetectionDidComplete;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Services\EventLogService;
use App\Http\Services\StorageService;
use App\Models\LogLevel;
use App\Models\Image;

class FaceDetectionDidCompleteHandler
{
    const STORAGE_ROOT = "images\\processed\\";
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(EventLogService $logService, StorageService $storageService)
    {
      $this->logService = $logService;
      $this->storageService = $storageService;

      $this->logService->LogApplicationEvent(LogLevel::Debug, "FaceDetectionDidCompleteHandler constructor");
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\FaceDetectionDidComplete  $event
     * @return void
     */
    public function handle(FaceDetectionDidComplete $event)
    {
      //At this point we have the original image data (this should be refactored to include a pointer to the image file instead),
      //the device ID, and a data structure that contains detected face geometries.
      //Call the storage service to persist this information.
      $this->logService->LogApplicationEvent(LogLevel::Info, "FaceDetectionDidCompleteHandler handler", $event);

      //Grab the image metadata from the database.
      $imageMetadata = \App\Models\Image::find($event->imageId);

      //If the image exists then we can move it from processing/ to a subfolder in processed/, and write the geometry information into
      //that subfolder as well.
      if($imageMetadata !== null)
      {
        $this->logService->LogApplicationEvent(LogLevel::Info, "Writing image data to processed folder.");

        try
        {
          $filePath = self::STORAGE_ROOT."\\".$event->imageId;
          $this->storageService->write($filePath.".txt", $event->imageData);
          $this->storageService->write($filePath.".json", json_encode($event->geometry));
        }
        catch(Exception $ex)
        {
          $this->logService->LogApplicationEvent(LogLevel::Error, "Exception occurred while writing image and/or metadata to storage: ".$ex->message);

          //Raise an event to inform the system that something went wrong.
        }
      }
      else
      {
        //Somehow the image metadata wasn't stored. This is a problem.
        $this->logService->LogApplicationEvent(LogLevel::Error, "Unable to retrieve metadata for image ID ".$event->imageId);

        //Signal the system that something really bad happened with this process.
      }
    }
}
