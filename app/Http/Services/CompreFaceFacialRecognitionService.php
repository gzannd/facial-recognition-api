<?php
namespace App\Http\Services;
use App\Http\Services\FacialRecognitionServiceBase;
use App\Events\FaceDetectionDidComplete;
use GuzzleHttp\Client;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;

class CompreFaceFacialRecognitionService extends FacialRecognitionServiceBase
{
  private $api_key = '49e7ade8-8128-4284-9976-564a893b663a';
  private $service_url = 'http://compreface-api:8080/api/v1/detection/detect/';
  private $timeout_in_seconds = 60.0;

  //Convert the JSON returned from the recognition service into a canonical data structure.
  private function ProcessResponse($data)
  {
    //  $this->logService->LogApplicationEvent(LogLevel:Info, "In CompreFaceFacialRecognitionService.ProcessResponse");
      $result = [];

      foreach($data as $detectedFace)
      {
          $detectionItem = new \stdClass;
          if(isset($detectedFace->mask))
          {
            $detectionItem->hasMask = !($detectedFace->mask->value == "without_mask");
          }
          $detectionItem->top = $detectedFace->box->y_min;
          $detectionItem->left = $detectedFace->box->x_min;
          $detectionItem->width = $detectedFace->box->x_max - $detectedFace->box->x_min;
          $detectionItem->height = $detectedFace->box->y_max - $detectedFace->box->y_min;

          $result[] = $detectionItem;
      }

      //$this->logService->LogApplicationEvent(LogLevel:Info, "CompreFaceFacialRecognitionService.ProcessResponse result", $result);

      return $result;
  }

  //Send the image to the recognition service.
  //Expects an Image model, which contains the base64 encoded image data and device information.
  public function ProcessImage($imageData, $imageId, $deviceId)
  {
    $this->logService->LogApplicationEvent(LogLevel::Info, "In CompreFaceFacialRecognitionService.ProcessImage");

    $client = new Client([
        'timeout'  => $this->timeout_in_seconds,
    ]);

    if(isset($imageData))
    {
      $this->logService->LogApplicationEvent(LogLevel::Info, "Calling CompreFace service at ".$this->service_url);

      $response = $client->post($this->service_url, [
        'headers' => [
          'x-api-key' => $this->api_key,
          'content-type' => 'application/json'
        ],
        'json' => ["file" => $imageData]
      ]);

      if(isset($response))
      {
          $this->logService->LogApplicationEvent(LogLevel::Info, "Received response", $response);

          if($response->getStatusCode() == 200 )
          {
            //Grab the JSON from the body and parse it into a canonical data structure.
            $returnVal = json_decode($response->getBody());
            $responseData = $this->ProcessResponse($returnVal->result);

            $this->logService->LogApplicationEvent(LogLevel::Info, "Response data", $responseData);

            //Call the base class's Complete method. This ensures that downstream processes are notified that
            //this task completed successfully.
            $this->Complete($imageData, $imageId, $deviceId, $responseData);

            return $responseData;
          }
          else
          {
            $this->Fail($imageId, $deviceId, "Facial detection service returned a status code of ".$response->getStatusCode());
            return null;
          }
      }
      else
      {
        $this->Fail($imageId, $deviceId, "Facial detection service did not return a response.");
        return null;
      }
    }
  }
}
