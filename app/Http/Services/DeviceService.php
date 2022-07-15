<?php
  namespace App\Http\Services;
  use App\Models\Device;
  use App\Interfaces\IDeviceService;

class DeviceService implements IDeviceService
{
    public function getDeviceById($id)
    {
        return $this->getDevice($id, 'id');
    }

    public function getDeviceBySystemId($id)
    {
        return $this->getDevice($id, 'system_id');
    }

    private function getDevice($id, $keyName)
    {
      $device = Device::where($keyName, "=", $id)
        ->with('event_data_type')->get()->first();

      return $device;
    }

    public function deviceIdExists($deviceId)
    {
      $device = Device::find($deviceId);
      return $device != null;
    }
}
