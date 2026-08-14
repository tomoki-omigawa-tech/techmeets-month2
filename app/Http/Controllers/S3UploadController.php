<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class S3UploadController extends Controller
{
    public function index()
    {
        return view('s3upload.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = Storage::disk('s3')->put('images', $request->file('image'),);
        $url = Storage::disk('s3')->url($path);

        return redirect()->route('s3upload.index')->with('url', $url);
    }
}
