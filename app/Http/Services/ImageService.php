<?php
namespace App\Http\Services;

class ImageService
{
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
