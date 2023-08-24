<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
{
    // serve static files from storage 
    public function show($filename)
    {
        return Storage::get('avatars/' . $filename);
    }
}
