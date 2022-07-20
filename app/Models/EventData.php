<?

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Device;


class EventData extends Model
{
    use HasFactory;

    protected $table "eventdata";
    protected $fillable = ["device_id", "data_type", "date_created_by_device"];

    public readonly $device;
    public readonly $data;
    public readonly $eventType;
    public readonly $timeStamp;

    public function __construct(Device $device, string $eventType, $data, $timeStamp)
    {
        $this->device = $device;
        $this->data = $data;
        $this->eventType = $eventType;
        $this->timeStamp = $timeStamp;
    }
}
