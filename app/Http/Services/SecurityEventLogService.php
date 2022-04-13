<?php
namespace App\Http\Services;
use App\Models\SecurityEventLogMessage;
use Illuminate\Support\Facades\Log;

class SecurityEventLogService
{
    public function Log(SecurityEventLogMessage $message)
    {
        //Insert the message into the database.
        $message->save();
    }

    public function Log()
    {}
}
?>
