<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class IconController extends Controller
{

    public function getIcon($subfolder = 'icon')
    {

        $directory = 'icon';

        switch($subfolder) {
            case 'svgs': $directory = 'icon/svgs';break;
            case 'svgs2': $directory = 'icon/svgs2';break;
            case 'brand': $directory = 'icon/svgs/brands';break;
            case 'duotone': $directory = 'icon/svgs/duotone';break;
            case 'light': $directory = 'icon/svgs/light';break;
            case 'regular': $directory = 'icon/svgs/regular';break;
            case 'sharp-light': $directory = 'icon/svgs/sharp-light';break;
            case 'sharp-solid': $directory = 'icon/svgs/sharp-solid';break;
            case 'sharp-regular': $directory = 'icon/svgs/sharp-regular';break;
            case 'solid': $directory = 'icon/svgs/solid';break;
            case 'thin': $directory = 'icon/svgs/thin';break;
            default: ;
        }

        // $files = Storage::disk('local')->allFiles($directory);

        $files = Cache::rememberForever($directory, function () use ($directory) {
            return Storage::disk('local')->allFiles($directory);
        });
        
        return response()
        ->json($files);
    }
}
