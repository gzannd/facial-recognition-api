<?
use Illuminate\Http\Request;
use App\Models\Device;

class EventDataFactory
{
  public function CreateEventData(Device $device, Request $request, string $requestType)
  {
      if($requestType == "SECURITY_CAMERA_IMAGE")
      {

      }
  }

  //Security camera images should 
  private function CreateSecurityCameraImageEventData(Device $device, Request $request)
  {

  }
}

?>
