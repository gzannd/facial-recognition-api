<?php
namespace App\Http\Factories;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\EventData;
use App\Models\ApplicationEventLogMessage;
use App\Models\LogLevel;
use App\Http\Services\EventLogService;
use App\Events\RawImageDataReceivedEvent;

class EventDataFactory
{
  public function __construct(EventLogService $eventLogService)
  {
    $this->eventLogService = $eventLogService;
  }

  public function CreateEventData(Device $device, Request $request, string $requestType)
  {
      $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Creating event data for type ".$requestType);
      switch($requestType)
      {
        case "SECURITY_CAMERA_IMAGE":
        {
            return $this->CreateSecurityCameraImageEventData($device, $request);
        }

        default:
          return null;
      }
  }

  //Security camera images should capture the raw base64 data as well as the device ID and timestamp.
  private function CreateSecurityCameraImageEventData(Device $device, Request $request)
  {
    $eventData = null;

    try
    {
      $imageData = null;
      $dateCreated = null;

      $imageData = $request->input("data");
      $dateCreated = $request->input("date_created");

      if($imageData != null && $dateCreated != null)
      {
        $eventData = new RawImageDataReceivedEvent($device, "SECURITY_CAMERA_IMAGE", $imageData, $dateCreated);
      }
      else
      {
        $errorMessage = "Invalid request: ";
        if($imageData == null)
        {
          $errorMessage." Missing image data.";
        }

        if($dateCreated == null)
        {
          $errorMessage."Missing creation date.";
        }

        $this->eventLogService->LogApplicationEvent(LogLevel::Error, $errorMessage);
      }
    }
    catch(Exception $e)
    {
      $error = "Invalid input JSON.";
      $this->eventLogService->LogApplicationEvent(LogLevel::Error, $error, $request);
    }

    return $eventData;
  }
}

?>
