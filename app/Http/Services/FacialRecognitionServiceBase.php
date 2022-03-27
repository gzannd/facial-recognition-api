<?php

namespace App\Http\Services;

use App\Interfaces\IFacialRecognitionService;
use App\Providers\FaceDetectionDidComplete;

class FacialRecognitionServiceBase implements IFacialRecognitionService
{
  public function ProcessImage($imageData, $imageId, $deviceId)
  {}

  protected function Complete($imageData, $imageId, $deviceId, $geometry)
  {
      event(new FaceDetectionDidComplete($imageData, $imageId, $deviceId, $geometry));
  }

  protected function Fail($imageId, $deviceId, $reason)
  {
    event(new FaceDetectionDidFail($imageId, $deviceId, $reason));
  }
}
?>
