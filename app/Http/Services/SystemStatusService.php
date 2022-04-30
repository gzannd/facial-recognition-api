<?php
namespace App\Http\Services;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use App\Models\SystemStatusModel;



class SystemStatusService
{
  public function __construct(
    EventLogService $eventLogService)
  {
      $this->eventLogService = $eventLogService;
  }

  public function getCurrentSystemStatus()
  {
    return new SystemStatusModel();
  }
}
?>
