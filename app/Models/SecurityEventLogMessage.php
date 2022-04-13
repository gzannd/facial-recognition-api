<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityEventLogMessage extends Model
{
    use HasFactory;

    protected $table = "security_event_log";

    public function __construct($deviceId, $deviceDate, $messageType, $data)
    {
      $this->device_id = $deviceId;
      $this->device_date = $deviceDate;
      $this->type = $messageType;
      $this->data = $data;
    }
}
