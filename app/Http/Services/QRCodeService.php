<?php
namespace App\Http\Services;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use QRcode;
require_once(dirname(dirname(dirname(__DIR__))).'/vendor/phpqrcode/qrlib.php');

class QRCodeService {
    public function __construct(EventLogService $eventLogService) 
    {
        $this->eventLogService = $eventLogService;
    }

    public function generateQRCode($data) 
    {
        $jsonData = json_encode($data);
        
        $slug = dechex( microtime(true) * 1000 ) . bin2hex( random_bytes(8) );
        $target = dirname(dirname(dirname(__DIR__)))."/storage/app/qr/auth/";

        $filePath = $target . 'output-qr-code-' . $slug . '.png';

        if (!file_exists($filePath)) {
            QRcode::png($jsonData, $filePath);
        }
    }
}
?>