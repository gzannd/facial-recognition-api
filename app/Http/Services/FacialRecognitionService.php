<?php
namespace App\Http\Services;
use GuzzleHttp\Client;

class FacialRecognitionService
{
  private $api_key = '49e7ade8-8128-4284-9976-564a893b663a';
  private $service_url = 'http://localhost:8000/api/v1/detection/';
  private $timeout_in_seconds = 60.0;

  //Convert the JSON returned from the recognition service into a canonical data structure.
  private function ProcessResponse($data)
  {
      $result = [];

      if($data->result !== null)
      {
        foreach($data->result as $detectedFace)
        {
            $detectionItem = new stdClass();
            $detectionItem->hasMask = !($detectedFace->mask->value == "without_mask");
            $detectionItem->top = $detectedFace->box->y_max;
            $detectionItem->left = $detectedFace->box->x_min;
            $detectionItem->width = $detectedFace->box->x_min + $detected_face->box->x_max;
            $detectionItem->height = $detectedFace->box->y_max + $detectedFace->box_y_min;

            $result[] = $detectionItem;
        }
      }

      return $result;
  }

  //Send the image to the recognition service.
  //Expects an Image model, which contains the base64 encoded image data and device information.
  public function ProcessImage($image)
  {
    $headers = ['x-api-key' => $this->api_key, 'content-type' => "application/json"];

    $client = new Client([
        'timeout'  => $this->timeout_in_seconds,
    ]);

    //Make sure the image actually contains data
    if(isset($image->encodedData))
    {
      $response = $client->request('POST', $this->service_url, $headers,
        [
          "d" => ["file" => $image->encodedData]
        ]);

      if(isset($response))
      {
          if($response->getStatusCode() == 200 && $response->getHeader("Content-Length")[0] > 0)
          {
            //Assume a successful response. Grab the JSON from the body.
            $body = $response->getBody();

            //Log the result and update the image database if necessary.

          }
      }
    }
  }
}
