<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClaim extends Model
{
    use HasFactory;
    protected $table = "user_claim";

    public $timestamps = false;

    protected $fillable = [
        'userId', 'claim', 'data'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'userId'
    ];

}
