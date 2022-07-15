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
use App\Http\Services\EventDataDispatchService;

class EventController extends Controller
{
  public function __construct(
    StorageService $storageService,
    EventLogService $eventLogService,
    IDeviceService $deviceService,
    EventDataDispatchService $eventDispatchService
    )
  {
      $this->storageService = $storageService;
      $this->eventLogService = $eventLogService;
      $this->deviceService = $deviceService;
      $this->eventDispatchService = $eventDispatchService;
  }

  public function postEvent(Request $request, $deviceId)
  {
    $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Event request received", $deviceId);

    //Validate the device ID
    $device = $this->deviceService->getDeviceById($deviceId);

    if($device == null)
    {
        return response("Device ID not found", 404);
    }
    else
    {
      $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Got device ", $device);
    }

    //Validate the event type is supported by the specified device ID.
    $eventDataType = $request->header("x-event-datatype");

    if($eventDataType == null || $device->doesSupportEventDataType($eventDataType) == false)
    {
      return response("Invalid event data type", 400);
    }

    $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Calling event dispatch service for ", $eventDataType);

    //Dispatch the event to the appropriate service class for further processing.
    $this->eventDispatchService->dispatch($eventDataType, null);

    return response("OK", 200);
  }
}
