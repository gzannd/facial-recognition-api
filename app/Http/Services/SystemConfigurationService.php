<?php
namespace App\Http\Services;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use App\Models\SystemConfiguration; 

class SystemConfigurationService {
    public function __construct(EventLogService $eventLogService) 
    {
        $this->eventLogService = $eventLogService;
    }
    
    private function getServerIP() 
    {
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        $res = socket_connect($sock, '8.8.8.8', 53);
        // You might want error checking code here based on the value of $res
        socket_getsockname($sock, $addr);
        socket_shutdown($sock);
        socket_close($sock);

        echo $addr;
        return $addr;
    }

    private function getServerPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    private function getAPIKey() 
    {
        return env("API_KEY");
    }

    public function getSystemConfiguration()
    {
        $config = new SystemConfiguration(); 
        $config->serverIP = $this->getServerIp();
        $config->port = $this->getServerPort();
        $config->apiKey = $this->getAPIKey();

        return $config;
    }
}
?>