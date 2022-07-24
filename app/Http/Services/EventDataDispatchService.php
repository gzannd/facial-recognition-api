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
    public function dispatch($data)
    {
      event(new RawImageDataReceivedEvent($data->device, $data->eventType, $data->imageData, $data->dateCreated));
      $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Event dispatched.");
    }
}
