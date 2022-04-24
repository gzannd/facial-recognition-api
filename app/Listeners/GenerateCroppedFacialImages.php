<?php

namespace App\Listeners;

use App\Events\FacialRecognitionGeometryCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Services\StorageService;
use App\Models\Image;
use App\Http\Services\ImageService;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;

class GenerateCroppedFacialImages
{
    const STORAGE_ROOT = "images\\processing\\";

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(StorageService $storageService, ImageService $imageService, EventLogService $eventLogService)
    {
        $this->storageService = $storageService;
        $this->imageService = $imageService;
        $this->eventLogService = $eventLogService;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\FacialRecognitionGeometryCreated  $event
     * @return void
     */
    public function handle(FacialRecognitionGeometryCreated $event)
    {
        $imageMetadata = \App\Models\Image::find($event->imageId);

        if($imageMetadata !== null)
        {
          $fileName = explode(".", $imageMetadata->file_path)[0];

          //Using the image ID in the event, grab the image data and the geometry data from the file system.
          $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Retrieving image geometry metadata for image ID ".$event->imageId);
          $geometry = $this->storageService->read($this::STORAGE_ROOT.$fileName.".json");

          if($geometry !== null)
          {
            $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Retrieving image metadata for image at path ".$imageMetadata->file_path);

            $imageData = $this->storageService->read($this::STORAGE_ROOT.$fileName.".jpeg");
            if($imageData !== null)
            {
              //Deserialize the geometry json.
              $geometryDTO = json_decode($geometry);

              //The geometry json must be an array.
              if(is_array($geometryDTO) == true)
              {
                //For each geometry element, create a cropped image and save it to the file system, using the image ID as a base for the filename.
                $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Generating ".count($geometryDTO)." cropped images.");

                $imageIndex = 1;
                foreach($geometryDTO as $croppedGeometry)
                {
                  $image = imagecreatefromstring(base64_decode($imageData));
                  if($image !== null)
                  {
                    $croppedImage = $this->imageService->Crop($image, $croppedGeometry->top, $croppedGeometry->left, $croppedGeometry->width, $croppedGeometry->height );
                    if($croppedImage !== null)
                    {
                        //Write the image to storage.
                        $croppedImageBase64 = $imageService->GdImageToBase64($croppedImage);
                        if($croppedImageBase64 !== null)
                        {
                          $this->storageService->write($this::STORAGE_ROOT.$fileName."_".$imageIndex.".jpeg", $croppedImageBase64);
                        }
                        else
                        {
                          //There was an issue converting the data for some reason.
                          $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Error generating cropped image.", $croppedGeometry);

                        }
                    }
                  }

                  $imageIndex = $imageIndex + 1;
                }

                $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Generated ".count($geometryDTO)." cropped images.");

                //Notify the system that the cropped images are created and available.

              }
              else
              {
                //JSON is not valid.
                $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Geometry JSON data must be an array.");
              }
            }
            else
            {
              $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Image data for image at path ".$imageMetadata->file_path." was not located in the file system.");
            }
          }
          else
          {
            $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Detection geometry metadata for image at path ".$imageMetadata->file_path." was not located in the file system.");
          }
        }
        else
        {

        }

        //For each geometry element, create a cropped image and save it to the file system, using the image ID as a base for the filename.

        //Notify the system that the cropped images are created and available.
    }
}
