<?php
namespace App\Http\Services;

class ImageService
{

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
