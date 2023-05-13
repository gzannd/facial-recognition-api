<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\QRCodeService;
use App\Http\Services\StorageService;
use App\Http\Services\EventLogService;
use App\Models\SecurityEventLogMessage;
use App\Models\ApplicationEventLogMessage;
use App\Models\LogLevel;
use App\Http\Services\SystemConfigurationService; 

class QRCodeController extends Controller
{
    public function __construct(
        SystemConfigurationService $systemConfigurationService,
        StorageService $storageService,
        EventLogService $eventLogService,
        QRCodeService $qrService)
      {
          $this->configuration = $systemConfigurationService->getSystemConfiguration();
          $this->qrService = $qrService;
          $this->eventLogService = $eventLogService;
      }

    public function systemConfig()
    {
        echo($this->configuration);
        $this->qrService->generateQRCode($this->configuration);
    }

    public function test() 
    {
        $this->eventLogService->LogApplicationEvent(LogLevel::Debug, "Attempting to generate QR code");
        $this->qrService->generateQRCode();

        return response()->json($result, 200);
    }
}