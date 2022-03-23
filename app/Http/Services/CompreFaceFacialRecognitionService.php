<?php
namespace App\Http\Services;
use App\Interfaces\IFacialRecognitionService;
use GuzzleHttp\Client;

class CompreFaceFacialRecognitionService implements IFacialRecognitionService
{
  private $api_key = '49e7ade8-8128-4284-9976-564a893b663a';
  private $service_url = 'http://compreface-api:8080/api/v1/detection/detect/';
  private $timeout_in_seconds = 60.0;

  //Convert the JSON returned from the recognition service into a canonical data structure.
  private function ProcessResponse($data)
  {
      $result = [];

      foreach($data as $detectedFace)
      {
          $detectionItem = new \stdClass;
          if(isset($detectedFace->mask))
          {
            $detectionItem->hasMask = !($detectedFace->mask->value == "without_mask");
          }
          $detectionItem->top = $detectedFace->box->y_max;
          $detectionItem->left = $detectedFace->box->x_min;
          $detectionItem->width = $detectedFace->box->x_max - $detectedFace->box->x_min;
          $detectionItem->height = $detectedFace->box->y_max - $detectedFace->box->y_min;

          $result[] = $detectionItem;
      }

      return $result;
  }

  //Send the image to the recognition service.
  //Expects an Image model, which contains the base64 encoded image data and device information.
  public function ProcessImage($image)
  {
    $client = new Client([
        'timeout'  => 30,
    ]);


    if(isset($image->data))
    {
      $response = $client->post($this->service_url, [
        'headers' => [
          'x-api-key' => $this->api_key,
          'content-type' => 'application/json'
        ],
        'json' => ["file" => $image->data]
      ]);

      if(isset($response))
      {
          if($response->getStatusCode() == 200 )
          {
            //Grab the JSON from the body and parse it into a canonical data structure.
            $returnVal = json_decode($response->getBody());
            $responseData = $this->ProcessResponse($returnVal->result);

            //Log the result.

            return $responseData;
          }
          else
          {
            //Log the failure.
          }
      }
    }
  }
}
