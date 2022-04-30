<?php

namespace App\Models;


class SystemStatusModel
{
    public function __construct()
    {
      $this->currentSystemTime = gmdate('Y-m-d H:i:s');
      $this->appName = "MaxLock Security System";
    }

    public string $appName = "MaxLock Security System";
    public string $apiVersion = "0.01 alpha";
    public string $status = "Normal";
    public string $currentSystemTime = "";
}
