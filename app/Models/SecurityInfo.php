<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityInfo extends Model
{
    use HasFactory;

    protected $guarded = ["person_id", "on_detection_action", "on_challenge_failure_action",
     "on_challenge_success_action", "images", "log", "challenge", "is_active"];
}
