<?php
namespace App\Utilities;

class ImageCropper
{
  protected $image;
  protected $format;

  public function __construct($imageData, $format)
  {
    $this->image = imagecreatefromstring(base64_decode($imageData));
    $this->format = $format;
  }

  public function Crop($geometry)
  {
    if($this->image !== false)
    {
      //Crop the image as per the geometry argument.
      $croppedImage = imagecrop($this->image, ['x' => $geometry->left, 'y' => $geometry->top, 'width' => $geometry->width, 'height' => $geometry->height]);

      //Convert the cropped image to base64.
      $croppedImageBase64 = $this->gdImgToBase64($croppedImage, $this->format);

      return $croppedImageBase64;
    }
    else
    {
      return null;
    }
  }

  private function gdImgToBase64( $gdImg, $format='jpg' )
  {
    // Validate Format
    if( in_array( $format, array( 'jpg', 'jpeg', 'png', 'gif' ) ) ) {

      ob_start();

      if( $format == 'jpg' || $format == 'jpeg' ) {

          imagejpeg( $gdImg );

      } elseif( $format == 'png' ) {

          imagepng( $gdImg );

      } elseif( $format == 'gif' ) {

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
              return $data;
          }
          else
          {
            return null;
          }
      }
  }

   // Failure
    return null;
  }

}

?>
