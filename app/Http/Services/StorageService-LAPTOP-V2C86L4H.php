<?php
namespace App\Http\Services;
use Illuminate\Support\Facades\Storage;

class StorageService
{
  private $disk = "local";

  public function __construct($disk = null)
  {
    if($disk !== null)
    {
      $this->disk = $disk;
    }
  }

  public function read($fileName)
  {
    if (Storage::disk($this->disk)->exists($fileName))
    {
      return Storage::disk($this->disk)->get($fileName);
    }
    else
    {
      return null;
    }
  }

  public function write($fileName, $data)
  {
     return Storage::disk($this->disk)->put($fileName, $data);
  }
}
