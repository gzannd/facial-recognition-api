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

                //The image data should be a base64 encoded string. Decode this string and deserialize it into a GDImage.
                $image = imagecreatefromstring(base64_decode($imageData));
                if($image !== FALSE)
                {
                  $imageIndex = 1;

                  //For each geometry item, crop it out of the base image and save it to the file system.
                  foreach($geometryDTO as $croppedGeometry)
                  {
                    try
                    {
                      if($image !== null)
                      {
                        $croppedImage = $this->imageService->Crop($image, $croppedGeometry->top, $croppedGeometry->left, $croppedGeometry->width, $croppedGeometry->height );
                        if($croppedImage !== null)
                        {
                            //Write the image to storage.
                            $croppedImageBase64 = $this->imageService->GdImageToBase64($croppedImage);
                            if($croppedImageBase64 !== null)
                            {
                              $filePath = $fileName."_".$imageIndex;
                              $extension = $this->imageService->GetExtensionForMimeType($imageMetadata->mime_type);

                              $this->storageService->write($this::STORAGE_ROOT.$filePath.".".$extension, $croppedImageBase64);

                              //Update the database.
                              $croppedImageDTO = new \App\Models\Image();
                              $croppedImageDTO->parent_id = $event->imageId;
                              $croppedImageDTO->date_created_by_device = date('Y-m-d H:i:s');
                              $croppedImageDTO->device_id = $imageMetadata->device_id;
                              $croppedImageDTO->file_path = $filePath;
                              $croppedImageDTO->mime_type = $imageMetadata->mime_type;

                              $croppedImageDTO->top = $croppedGeometry->top;
                              $croppedImageDTO->left = $croppedGeometry->left;
                              $croppedImageDTO->width = $croppedGeometry->width;
                              $croppedImageDTO->height = $croppedGeometry->height;
                              $croppedImageDTO->description = "Cropped from primary image.";
                              $croppedImageDTO->data = null;

                              $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Saving cropped image metadata ".$imageIndex." to database.");
                              $croppedImageDTO->save();
                            }
                            else
                            {
                              //There was an issue converting the data.
                              $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Error converting cropped image ".$imageIndex." to base64.", $croppedGeometry);
                            }
                        }
                        else
                        {
                          $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Error cropping image ".$imageIndex.".", $croppedGeometry);
                        }
                      }
                    }
                    catch(Exception $ex)
                    {
                      $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Exception occurred while cropping image ".$imageIndex.". ".$ex->message, $croppedGeometry);
                    }

                    $imageIndex = $imageIndex + 1;
                  }

                  $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Generated ".count($geometryDTO)." cropped images.");

                  //Notify the system that the cropped images were successfully saved.

                }
                else
                {
                  $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Base image data is not a valid file format. Expecting a base64 encoded image.");
                }
              }
              else
              {
                $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Detection geometry data at path ".$imageMetadata->file_path." is not valid. It must be a JSON array.");
              }
            }
            else
            {
              $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Base image data at path ".$imageMetadata->file_path." was not located in the file system.");
            }
          }
          else
          {
            $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Detection geometry data at path ".$imageMetadata->file_path." was not located in the file system.");
          }
        }
        else
        {
          $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Base image metadata for image ID ".$event->imageId." was not found.");
        }
    }
}
