<?php
namespace App\Http\Services;
use App\Models\SecurityEventLogMessage;
use App\Models\ApplicationEventLogMessage;
use App\Models\LogLevel;
use App\Http\Services\EventLogService;
use App\Events\RawImageDataReceivedEvent;

class EventDataDispatchService
{

  public function __construct(EventLogService $eventLogService)
  {
    $this->eventLogService = $eventLogService;
  }

    //Dispatch an event to the appropriate service.
    public function dispatch($eventType, $data)
    {
      $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Dispatching event ".$eventType, $data);

      switch($eventType)
      {
        case "SECURITY_CAMERA_IMAGE":
          RawImageDataReceivedEvent::dispatch($data, $eventType);
          $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Event ".$eventType." dispatched.");
          break;
      }
    }
}
