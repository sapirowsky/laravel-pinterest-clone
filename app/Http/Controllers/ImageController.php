<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index(){
        return Inertia::render('Uploader');
    }
    public function show(){
        return Image::latest()->select(['name', 'id', 'creator'])->get();
    }
    public function showCurrentUserImages(){
        return Image::where('creator', '=', Auth::id())->latest()->select(['name', 'id', 'creator'])->get();
    }
    public function store(Request $request){
        // validate request
        if(!$request->hasFile('image')){
            return response()->json(['error' => 'There is no image present'], 400);
        }

        $request->validate([
            'image' => 'required|file|image'
        ]);

        // store image
        $image = $request->file('image');
        $image_extension = 'webp';
        $image_name = time().'_'.$image->getClientOriginalName();
        $image_name = str_replace('.'.$image->extension(), '.'.$image_extension, $image_name);
        $path = public_path('storage/images'. '/'.$image_name);

        $savedImage = \ImageEdt::make($image)->save($path);
        
        // Create image model
 
        $image = Image::create([
            'creator' => Auth::id(),
            'name' => $image_name,
            'ext' => $image_extension,
            'size' => $savedImage->filesize(),
        ]);
     
        // return image model
        return $image->name;
    }
    public function destroy(Request $request){
        $imageId = $request->integer('imageId');
        $image = Image::where('id', '=', $imageId)->latest()->pluck('name', 'id')->toArray();
        if(Storage::delete('public/images/'.$image[$imageId]) && Image::destroy($imageId)){
            return response()->json(['success' => 'File deleted successfuly.'], 200);
        }else{
            return response()->json(['error' => 'Somthing went wrong while deleting file.'], 500);
        }        
    }
}
