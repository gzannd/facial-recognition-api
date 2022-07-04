<?php
  namespace App\Http\Services;
  use App\Models\Device;
  use App\Interfaces\IDeviceService;

class DeviceService implements IDeviceService
{
    public function getDeviceById($deviceId)
    {
        $device = Device::find($deviceId)
          ->with('event_data_type')->get()->toArray();
        return $device;
    }

    public function deviceIdExists($deviceId)
    {
      $device = Device::find($deviceId);
      return $device != null;
    }
}
