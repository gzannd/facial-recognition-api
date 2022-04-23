<?php

namespace App\Http\Services;

use App\Interfaces\IFacialRecognitionService;
use App\Events\FaceDetectionDidComplete;
use App\Events\FaceDetectionDidFail;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;

class FacialRecognitionServiceBase implements IFacialRecognitionService
{
  public function __construct(EventLogService $logService)
  {
    $this->logService = $logService;
  }

  protected $logService;

  public function ProcessImage($imageData, $imageId, $deviceId)
  {}

  protected function Complete($imageData, $imageId, $deviceId, $geometry)
  {
      $this->logService->LogApplicationEvent(LogLevel::Info, "FacialRecognitionServiceBase Complete. Image Id ".$imageId.", Device Id ".$deviceId, $geometry);
      event(new FaceDetectionDidComplete($imageData, $imageId, $deviceId, $geometry));
  }

  protected function Fail($imageId, $deviceId, $reason)
  {
    $this->logService->LogApplicationEvent(LogLevel::Info, "FacialRecognitionServiceBase Fail. Image Id ".$imageId.", Device Id ".$deviceId, $reason);
    event(new FaceDetectionDidFail($imageId, $deviceId, $reason));
  }
}
?>
