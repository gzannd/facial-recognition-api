<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDataType extends Model
{
    use HasFactory;

    protected $table = "event_data_type";

    public function device_data_types()
    {
        return $this->belongsTo('App\Models\Device');
    }
}
