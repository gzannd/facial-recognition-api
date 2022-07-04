<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ["name", "description", "type", "system_id"];

    public function event_data_type()
    {
        return $this->hasMany('App\Models\EventDataType');
    }
}
