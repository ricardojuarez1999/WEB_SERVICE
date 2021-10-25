<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{

    public static function createImage($filename, $url, $type)
    {
        $image = new Image();
        $image->filename = $filename;
        $image->url = $url;
        $image->type = $type;
        $image->save();

        return $image;
    }

}
