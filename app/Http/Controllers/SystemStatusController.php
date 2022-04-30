<?php
namespace App\Http\Controllers;

use App\Http\Services\EventLogService;
use App\Http\Services\SystemStatusService;
use App\Models\LogLevel;
use App\Models\SystemStatusModel;
use Illuminate\Http\Request;

class SystemStatusController extends Controller
{
  public function __construct(
    SystemStatusService $SystemStatusService,
    EventLogService $eventLogService)
  {
      $this->SystemStatusService = $SystemStatusService;
      $this->eventLogService = $eventLogService;
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function getCurrentSystemStatus()
  {
    $result = $this->SystemStatusService->getCurrentSystemStatus();

    return response()->json($result, 200);
  }
}
