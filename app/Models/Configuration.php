<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $table = "configuration";
    protected $fillable = ["primary_user_id", "secondary_users", "system_description"];
    protected $guarded = ["system_identifier"];

    public function primaryUser()
    {
      return $this->hasOne("App\Models\Person", "id", "primary_user_id");
    }

    public function secondaryUsers()
    {
      return $this->hasMany("App\Models\Person", "id", "secondary_users");
    }
}
