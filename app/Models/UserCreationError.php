<?php

namespace App\Models;

class UserCreationError
{
    public function __construct(String $reason, Int $errorCode = 0)
    {
        $this->reason = $reason;
        $this->errorCode = $errorCode;
    }
}

?>