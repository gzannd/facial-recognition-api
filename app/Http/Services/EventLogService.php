<?php
namespace App\Http\Services;
use App\Models\SecurityEventLogMessage;
use App\Models\ApplicationEventLogMessage;
use App\Models\LogLevel;
use Illuminate\Support\Facades\Log;

class EventLogService
{
    public function LogSecurityEvent(SecurityEventLogMessage $message)
    {
        //Insert the message into the database.
        $message->save();
    }

    public function LogApplicationEvent(ApplicationEventLogMessage $message)
    {

      switch($message->level)
      {
          case LogLevel::Alert:
            Log::alert($message->message);
            break;
          case LogLevel::Critical:
            Log::critical($message->message);
            break;
          case LogLevel::Error:
            Log::error($message->message);
            break;
          case LogLevel::Warning:
            Log::warning($message->message);
            break;
          case LogLevel::Info:
            Log::info($message->message);
            break;
          case LogLevel::Debug:
            Log::debug($message->message);
            break;
          default:
            //Unknown log type. Log it as Info.
            Log::info($message->message);
            break;
      }
    }
}
?>
