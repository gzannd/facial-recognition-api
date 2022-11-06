<?

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    public function __construct($userIds, $title, $body)
    {
      $this->userIds = $userIds;
      $this->title = $title;
      $this->body = $body;
    }
}

?>
