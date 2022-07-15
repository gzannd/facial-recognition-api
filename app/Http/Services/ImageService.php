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

class ImageService:RawImageDataReceivedListener
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
      $this->STORAGE_ROOT = config('globals.IMAGE_PROCESSING_STORAGE_ROOT');
    }

    public function handle(RawImageDataReceivedEvent $event)
    {
      $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Image service received raw image data event.", $event);
    }

    public void processImageData($deviceId, $dateCreated, $mimeType, $description, $imageData)
    {
      //Create a new Image model and populate it.
      $image = new \App\Models\Image();
      $image->date_created_by_device = $this->convertDateTime($dateCreated);
      $image->device_id = $deviceId;
      $image->mime_type = $mime_type;
      $image->description = null;
      $image->data = null;

      //Save the image to disk and note its filename.
      $image->file_path = $this->createImageFilename($image);

      //Save the raw image data to the file system.
      $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Saving image to file system.");
      $extension = $this->imageService->GetExtensionForMimeType($mime_type);

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
