<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\SecurityEventLogMessage;
use App\Models\ApplicationEventLogMessage;
use App\Models\LogLevel;
use App\Http\Services\StorageService;
use App\Http\Services\EventLogService;
use App\Interfaces\IDeviceService;

class EventController extends Controller
{
  public function __construct(
    StorageService $storageService,
    EventLogService $eventLogService,
    IDeviceService $deviceService
    )
  {
      $this->storageService = $storageService;
      $this->eventLogService = $eventLogService;
      $this->deviceService = $deviceService;
  }

  public function postEvent(Request $request, $deviceId, $eventType)
  {
    $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Event request received", $deviceId." ".$eventType);

    //Validate the device ID
    $device = $this->deviceService->getDeviceById($deviceId);

    if($device == null)
    {
        return response("Device ID not found", 404);
    }
    else
    {
      $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Got device ", var_dump($device));
    }
    //Validate the event type is valid for the specified device ID.

    //Dispatch the event to the appropriate service class for further processing.


    return response("OK", 200);
  }
}
