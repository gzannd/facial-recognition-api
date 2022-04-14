<?php
namespace App\Http\Services;
use App\Models\SecurityEventLogMessage;
use App\Models\ApplicationEventLogMessage;
use App\Models\LogLevel;
use Illuminate\Support\Facades\Log;

class EventLogService
{
    public function LogSecurityEvent($deviceId, $deviceDate, string $level, string $message)
    {
        //Insert the message into the database.
        $logMessage = new SecurityEventLogMessage($deviceId, $deviceDate, $level, $message);
        $logMessage->save();
    }

    public function LogApplicationEvent(LogLevel $level, string $message, $data = null)
    {
      if(isset($data) == false)
      {
        switch($level)
        {
            case LogLevel::Alert:
              Log::alert($message);
              break;
            case LogLevel::Critical:
              Log::critical($message);
              break;
            case LogLevel::Error:
              Log::error($message);
              break;
            case LogLevel::Warning:
              Log::warning($message);
              break;
            case LogLevel::Info:
              Log::info($message);
              break;
            case LogLevel::Debug:
              Log::debug($message);
              break;
            default:
              //Unknown log type. Log it as Info.
              Log::info($message);
              break;
        }
      }
      else
      {
        $contextData = $data;
        if(is_array($contextData) == false)
        {
          $contextData = [$data];
        }

        switch($level)
        {
            case LogLevel::Alert:
              Log::alert($message, $contextData);
              break;
            case LogLevel::Critical:
              Log::critical($message, $contextData);
              break;
            case LogLevel::Error:
              Log::error($message, $contextData);
              break;
            case LogLevel::Warning:
              Log::warning($message, $contextData);
              break;
            case LogLevel::Info:
              Log::info($message, $contextData);
              break;
            case LogLevel::Debug:
              Log::debug($message, $contextData);
              break;
            default:
              //Unknown log type. Log it as Info.
              Log::info($message, $contextData);
              break;
        }
      }
    }
}
?>
