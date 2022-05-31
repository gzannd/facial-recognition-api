<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageControllerConfiguration extends Model
{
  use HasFactory;

  public $IMAGE_LOOKBACK_MINUTES = 30;
}
?>
