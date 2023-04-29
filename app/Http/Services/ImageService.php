<?php
namespace App\Http\Services;
use App\Models\ApplicationEventLogMessage;
use App\Models\LogLevel;
use App\Http\Services\EventLogService;
use App\Http\Services\StorageService;
use App\Models\Image;
use App\Models\Device;
use App\Http\Services\ImageUtilities;
use App\Http\Services\DateUtilities;
use App\Jobs\SendImageToDetectionService;
use App\Listeners\RawImageDataReceivedListener;
use App\Events\RawImageDataReceivedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ImageService
{
  public function __construct(
    StorageService $storageService,
    EventLogService $eventLogService,
    ImageUtilities $imageUtilities,
    DateUtilities $dateUtilities)
    {
      $this->dateUtilities = $dateUtilities;
      $this->imageUtilities = $imageUtilities;
      $this->storageService = $storageService;
      $this->eventLogService = $eventLogService;
      $this->dateUtilities = $dateUtilities;
      $this->STORAGE_ROOT = config('globals.IMAGE_PROCESSING_STORAGE_ROOT');
    }

    public function handle($event)
    {
      //$this->eventLogService->LogApplicationEvent(LogLevel::Info, "Image service received raw image data event: ", $event);
      if($event->device != null && $event->imageData !== null && $event->dateCreated !== null)
      {
          //Process the raw image data.
          $imageInfo = $this->getImageInfo($event->imageData);

          $this->processImageData($event->device->id, $event->dateCreated, $imageInfo->mime_type, "", $imageInfo->data);
      }
      else
      {
        //Received an incomplete event object. Log the issue.
        $errorMessage = "Incomplete event object received: ";
        if($event->device == null) $errorMessage .= " device data, ";
        if($event->imageData == null) $errorMessage .= " image data, ";
        if($event->dateCreated == null) $errorMessage .= " create date";

        $this->eventLogService->LogApplicationEvent(LogLevel::Error, $errorMessage);
      }
    }

    public function getImageById($imageId)
    {
      $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Attempting to retrieve image ID ".$imageId."...");
      $image = $this->imageUtilities->GetImageById($imageId);

      return $image;
    }

    public function processImageData($deviceId, $dateCreated, $mimeType, $description, $imageData)
    {
      //Create a new Image model and populate it.
      $image = new \App\Models\Image();
      $image->date_created_by_device = $this->dateUtilities->convertDateTime($dateCreated);
      $image->device_id = $deviceId;
      $image->mime_type = $mimeType;
      $image->description = null;
      $image->data = null;

      //Save the image to disk and note its filename.
      $image->file_path = $this->createImageFilename($image);

      //Save the raw image data to the file system.
      $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Saving image to file system.");
      $extension = $this->imageUtilities->GetExtensionForMimeType($mimeType);

      $this->storageService->write($this->STORAGE_ROOT.$image->file_path.".".$extension, $imageData);

      //Save the metadata to disk.
      $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Saving image metadata to database.");
      $image->save();

      //Kick off a facial recognition task.
      $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Sending image to facial detection service.");
      SendImageToDetectionService::dispatch($imageData, $image->id, $image->device_id, $this->eventLogService);

      return $image;
    }

    private function createImageFilename($imageData)
    {
        return uniqid();
    }

    private function getImageInfo($base64Data)
    {
      $result = null;
      $data = explode(',', $base64Data)[1];
      $binary = base64_decode(explode(',', $base64Data)[1]);
      $imageInfo = getimagesizefromstring($binary);
      if($imageInfo !== null)
      {
        $result = new \stdClass();
        $result->width = $imageInfo[0];
        $result->height = $imageInfo[1];
        $result->mime_type = $imageInfo["mime"];
        $result->data = $data;
      }

      return $result;
    }
}


 ?>
