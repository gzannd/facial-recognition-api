<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationEventLogMessage extends Model
{
    use HasFactory;

    public function __construct(LogLevel $level, string $message, array $data = null)
    {
      $this->level = $level;
      $this->message = $message;
      $this->data = $data;
    }
}
