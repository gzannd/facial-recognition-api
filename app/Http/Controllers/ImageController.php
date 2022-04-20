<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Device;
use App\Models\SecurityEventLogMessage;
use App\Models\ApplicationEventLogMessage;
use App\Models\LogLevel;
use App\Http\Services\StorageService;
use App\Interfaces\IFacialRecognitionService;
use App\Jobs\SendImageToDetectionService;
use App\Providers\FaceDetectionDidComplete;
use App\Utilities\ImageCropper;
use App\Http\Services\EventLogService;

class ImageController extends Controller
{
    public function __construct(
      StorageService $storageService,
      IFacialRecognitionService $recognitionService,
      EventLogService $eventLogService)
    {
        $this->storageService = $storageService;
        $this->recognitionService = $recognitionService;
        $this->eventLogService = $eventLogService;
    }

    const STORAGE_ROOT = "images\\";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexByDevice(Request $request, $deviceId)
    {
      $limit = $request->query('limit', 10);

      //Get all of the parent images. The query will automagically pull any related child images
      //into the children property for each parent.
      $result = \App\Models\Image::where('device_id', $deviceId)
        ->with('detected_faces')
        ->where('parent_id', null)
        ->orderby('date_created_by_device', 'desc')
        ->take($limit)
        ->get();

      return response()->json($result, 200);
    }

    public function indexByPerson(Request $request, $personId)
    {
      $limit = $request->query('limit', 10);

      //Get all of the parent images whose children (if any) have a detected face associated with the specified person.
      //The query will automagically pull any related child images into the children property for each parent.
      $result = \App\Models\Image::where('parent_id', null)
        ->with('detected_faces')
          ->whereHas('detected_faces', function ($query) use ($personId) {
                     $query->where('person_id', $personId);})
        ->orderby('date_created_by_device', 'desc')
        ->take($limit)
        ->get();

      return response()->json($result, 200);
    }


    /**
     * Show the form for creating a new resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $deviceId)
    {
      $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Device Id ".$deviceId);
      $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Request received");

      /*This method expects a javascript structure that conforms to this format:
      {
        "device_id":xxx,
        "main_image":{
          "data":Base64 encoded string,
          "date_created":Date string
        }
      }
      The base64 encoded image strings must contain the image MIME type.
      */


      $error = null;
      $extension = null;

      //Check the device ID to make sure it's valid.
      if($this->deviceIdExists($deviceId) == false)
      {
          $error = "Invalid device Id";
          $this->eventLogService->LogApplicationEvent(LogLevel::Error, $error." ".$deviceId, $request);
          return response()->json($error, 400);
      }

      try
      {
        $imageData = $request->input("main_image")["data"];
      }
      catch(Exception $e)
      {
        $error = "Invalid input JSON.";
        $this->eventLogService->LogApplicationEvent(LogLevel::Error, $error, $request);
        return response()->json($error);
      }

      if($error == null)
      {
        //Get the extension.
        try
        {
          $extension = explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
        }
        catch(Exception $e)
        {
          $error = "Missing image MIME type.";
          $this->eventLogService->LogApplicationEvent(LogLevel::Error, $error, $request);
          return response()->json($error);
        }
      }

      if($error == null)
      {
        try
        {
          //Get the info for the main image.
          $mainImageInfo = $this->getImageInfo($imageData);

          //Create a new Image model and populate it.
          $image = new \App\Models\Image();
          $image->date_created_by_device = $this->convertDateTime($request->input("main_image")["date_created"]);
          $image->device_id = $deviceId;
          $image->mime_type = $mainImageInfo->mime_type;
          $image->description = null;
          $image->data = null;

          //Save the image to disk and note its filename.
          $image->file_path = $this->createImageFilename($image);

          //Save the raw image data to the file system.
          $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Saving image to file system.");
          $this->storageService->write(self::STORAGE_ROOT.$image->file_path, $mainImageInfo->data);

          //Save the metadata to disk.
          $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Saving image metadata to database.");
          $image->save();

          //Kick off a facial recognition task.
          $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Sending image to facial detection service.");
          SendImageToDetectionService::dispatch($mainImageInfo->data, $image->id, $image->device_id, $this->eventLogService);
        }
        catch(Exception $e)
        {
          //Log the error and return a 500.
          $this->eventLogService->LogApplicationEvent(LogLevel::Error, $e->getMessage());
          return response()->json("An internal server error has occurred", 500);
        }
      }
      else
      {
        $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Bad request", $request);
        return response()->json("Bad request", 400);
      }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*This method expects a javascript structure that conforms to this format:
        {
          "device_id":xxx,
          "main_image":{
            "data":Base64 encoded string,
            "date_created":Date string
          },
          "face_images":[
            {
                "top":xxx,
                "left":xxx,
                "person_id":xxx,
                "data": Base64 encoded string
            },
            ...
          ]
        }
        The base64 encoded image strings must contain the image MIME type.
        */

        $error = null;
        $extension = null;

        try
        {
          $imageData = $request->input("main_image")["data"];
        }
        catch(Exception $e)
        {
          $error = "Invalid input JSON.";
        }

        if($error == null)
        {
          //Get the extension.
          try
          {
            $extension = explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
          }
          catch(Exception $e)
          {
            $error = "Missing image MIME type.";
          }
        }

        if($error == null)
        {
          //Get the info for the main image.
          $mainImageInfo = $this->getImageInfo($imageData);

          //Create a new Image model and populate it.
          $image = new \App\Models\Image();
          $image->date_created_by_device = $this->convertDateTime($request->input("main_image")["date_created"]);
          $image->device_id = $request->input("device_id");
          $image->mime_type = $mainImageInfo->mime_type;
          $image->data = "";

          //Get the info for all of the detected faces.
          foreach($request->input("main_image")["face_images"] as $image)
          {
              $imageInfo = $this->getImageInfo($image);

              foreach (get_object_vars($imageInfo) as $key => $value) {
                $image->$key = $value;
              }
          }

          //If all went well then we can store these images.
          //Generate a filename for this image, then save it to the database .
          $decodedData = $this->decodeBase64ImageData($request->input("main_image")["data"]);

          if($decodedData == null)
          {
            //Image data isn't valid. Return an error.
            $error = "Invalid image data.";
          }
          else
          {
            $fileName = $this->createImageFilename($image);
            $image->file_path = $this::STORAGE_ROOT.$fileName;
            $image->description = "test image";

            $this->storageService->write($image->file_path, $decodedData);
            $image->save();
          }

          foreach($request->input("main_image")["face_images"] as $image)
          {
              $croppedImage = new \App\Models\Image();
              $croppedImageData = $this->decodeBase64ImageData($image->data);
              $croppedImage->date_created_by_device = date($image->date_created);
              $croppedImage->parent_id = $image->id;
              $croppedImage->device_id = $request->input("device_id");
              foreach (get_object_vars($image) as $key => $value) {
                $croppedImage->$key = $value;
              }

              $fileName = $this->createImageFilename($croppedImage);
              $croppedImage->file_path = $this::STORAGE_ROOT.$fileName;
              $this->storageService->write($croppedImage->file_path, $croppedImageData);
              $croppedImage->save();
          }
        }
        else
        {
          echo($error);
        }
    }

    private function deviceIdExists($deviceId)
    {
      $device = Device::find($deviceId);
      return $device != null;
    }

    private function convertDateTime($dateTime)
    {
      try
      {
        return date( 'Y-m-d H:i:s', strtotime(str_replace('-', '/', $dateTime)));
      }
      catch(Exception)
      {
        return null;
      }
    }

    private function getExtension($mime_type)
    {
      $extensions = array('image/jpeg' => 'jpeg',
                          'image/png' => 'png');

      return $extensions[strtolower($mime_type)];
    }

    private function createImageFilename($imageData)
    {
        return uniqid().".".$this->getExtension($imageData->mime_type);
    }

    private function decodeBase64ImageData($base64)
    {
      $exploded = explode(',', $base64, 2);
      $encoded = $exploded[1];
      $decoded = base64_decode($encoded);

      return $decoded;
    }

    private function getImageInfo($base64Data)
    {
      $result = null;
      $data = explode(',', $base64Data)[1];
      $binary = base64_decode(explode(',', $base64Data)[1]);
      $imageInfo = getimagesizefromstring($binary);
      if($imageInfo !== null)
      {
        $result = new \stdClass();
        $result->width = $imageInfo[0];
        $result->height = $imageInfo[1];
        $result->mime_type = $imageInfo["mime"];
        $result->data = $data;
      }

      return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
