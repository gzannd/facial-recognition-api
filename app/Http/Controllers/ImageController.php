<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    public function __construct(\App\Http\Services\StorageService $storageService)
    {
        $this->storageService = $storageService;

        var_dump($this->storageService);
    }

    const STORAGE_ROOT = "images\\";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            //$path = Storage::disk($this->storageLocation)->put($this::STORAGE_ROOT.$fileName, $decodedData);
            $image->file_path = $this::STORAGE_ROOT.$fileName;
            $image->description = "test image";

            $this->storageService->write($image->file_path, $decodedData);
            $image->save();
          }

          foreach($request->input("main_image")["face_images"] as $image)
          {
              $croppedImage = $this->decodeBase64ImageData($image->data);
              $croppedImage->date_created_by_device = date($image->date_created);
              $croppedImage->parent_id = $image->id;
              $croppedImage->device_id = $request->input("device_id");
              foreach (get_object_vars($image) as $key => $value) {
                $croppedImage->$key = $value;
              }

              $fileName = $this->createImageFilename($croppedImage);
              $path = Storage::disk($this->storageLocation)->put($this::STORAGE_ROOT.$fileName, $decodedData);
              $croppedImage->file_path = $fileName;
              $croppedImage->save();
          }
        }
        else
        {
          echo($error);
        }
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
      $binary = base64_decode(explode(',', $base64Data)[1]);
      $imageInfo = getimagesizefromstring($binary);
      if($imageInfo !== null)
      {
        $result = new \stdClass();
        $result->width = $imageInfo[0];
        $result->height = $imageInfo[1];
        $result->mime_type = $imageInfo["mime"];
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
