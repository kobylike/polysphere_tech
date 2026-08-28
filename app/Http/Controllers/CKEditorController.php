<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:5120', // 5MB max
        ]);

        $file = $request->file('upload');
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('posts/inline-images', $filename, 'public');

        $url = Storage::url($path);

        return response()->json([
            'url' => $url,
        ]);
    }
}
