<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ["name", "description", "type", "system_id"];

    public function doesSupportEventDataType($eventDataType)
    {
        $found = false;

        if(isset($this->event_data_type))
        {
          foreach($this->event_data_type as $item)
          {
            if(strcasecmp($item->name, $eventDataType) == 0)
            {
                $found = true;
                break;
            }
          }
        }

        return $found;
    }

    public function event_data_type()
    {
        return $this->hasMany('App\Models\EventDataType');
    }
}
