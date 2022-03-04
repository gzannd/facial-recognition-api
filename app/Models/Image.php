<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    //Set up a self-referential relationship. This technically allows unlimited nesting of images, but we'll never go more than
    //one level deep (main image + cropped face images).
    public function main()
    {
        return $this->belongsTo('\App\Models\Image','parent_id')->where('parent_id',0)->with('main');
    }

    public function detected_faces()
    {
        return $this->hasMany('\App\Models\Image','parent_id')->with('detected_faces');
    }
}
