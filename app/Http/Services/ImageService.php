<?php
namespace App\Http\Services;
use App\Models\ApplicationEventLogMessage;
use App\Models\LogLevel;
use App\Http\Services\EventLogService;
use App\Http\Services\StorageService;
use App\Models\Image;

class ImageService
{
  public function __construct(
    StorageService $storageService,
    EventLogService $eventLogService,)
    {
      $this->storageService = $storageService;
      $this->eventLogService = $eventLogService;
      $this->STORAGE_ROOT = config('globals.IMAGE_PROCESSING_STORAGE_ROOT');
    }

  public function GetImageById($imageId)
  {
    $base64 = null;

    $imageMetadata = Image::find($imageId);

    if($imageMetadata !== null)
    {
      $base64 = $this->storageService->read($this->STORAGE_ROOT.$imageMetadata->file_path.".".$this->GetExtensionForMimeType($imageMetadata->mime_type));
    }

    if($base64 !== null)
    {
      $imageMetadata->base64 = $base64;
      return $imageMetadata;
    }
    else
    {
      return null;
    }
  }

  //Encodes a GDImage to base64.
  public function GdImageToBase64($image, $format="jpg")
  {
    if( in_array( $format, array( 'jpg', 'jpeg', 'png', 'gif' ) ) )
    {
        ob_start();
        if( $format == 'jpg' || $format == 'jpeg' )
        {
          imagejpeg( $image );
        } elseif( $format == 'png' )
        {
            imagepng( $image );
        } elseif( $format == 'gif' )
        {
            imagegif( $image );
        }

        $data = ob_get_contents();
        ob_end_clean();

        // Check for gd errors / buffer errors
        if( !empty( $data ) )
        {
          $data = base64_encode( $data );

          // Check for base64 errors
          if ( $data !== false )
          {
            // Success
            return $data;
          }
        }
    }

    // Failure
    return null;
  }

  public function GetExtensionForMimeType($mimeType)
  {
      static $extensions = array('image/jpeg' => 'jpeg',
                          'image/png' => 'png');

      return $extensions[strtolower($mimeType)];
  }

  public function Crop($image, $top, $left, $width, $height)
  {
    $result = imagecrop($image, ['x' => $left, 'y' => $top, 'width' => $width, 'height' => $height]);

    if($result !== FALSE)
    {
      return $result;
    }
    else
    {
      return null;
    }
  }
}
?>
