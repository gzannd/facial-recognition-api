<?php
namespace App\Interfaces;

interface IDeviceService
{
  public function getDeviceById($deviceId);
  public function deviceIdExists($deviceId);
}
?>
