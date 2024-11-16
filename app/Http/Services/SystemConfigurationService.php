<?php
namespace App\Http\Services;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use App\Models\SystemConfiguration; 
use DateTime;

class SystemConfigurationService {
    public function __construct(EventLogService $eventLogService) 
    {
        $this->eventLogService = $eventLogService;
    }
    
    private function getServerIP() 
    {
        return getHostByName(getHostName());
        //return "192.168.0.141";
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
        $config->version = "0.01 alpha";
        $config->expiration = date("Y-m-d H:i:s", strtotime('+1 hours'));
        $config->ipAddress = $this->getServerIP();
        return $config;
    }
}
?>