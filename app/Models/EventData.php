<?

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventData extends Model
{
    use HasFactory;

    protected $table "eventdata";
    protected $fillable = ["device_id", "data_type", "date_created_by_device"];
}
