<?php
namespace App\Http\Services;

class ImageService
{

  public function GdImageToBase64($image, $format="jpg")
  {
    if( in_array( $format, array( 'jpg', 'jpeg', 'png', 'gif' ) ) )
    {
        ob_start();
        if( $format == 'jpg' || $format == 'jpeg' )
        {
            imagejpeg( $gdImg );
        } elseif( $format == 'png' )
        {
            imagepng( $gdImg );
        } elseif( $format == 'gif' )
        {
            imagegif( $gdImg );
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
