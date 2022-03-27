<?php
namespace App\Interfaces;

interface IFacialRecognitionService
{
  public function ProcessImage($imageData, $deviceId, $imageId);
}
?>
