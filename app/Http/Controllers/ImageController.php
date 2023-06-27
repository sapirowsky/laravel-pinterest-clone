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
        $uploadedFile = \ImageEdt::make($image);
        return ;
        $path = $uploadedFile->store('public/images');

        if(!$path ){
            return response()->json(['error' => 'The file could not be saved.'], 500);
        }
        $uploadedFile = $request->file('image');
        
        // Create image model
 
        $image = Image::create([
            'creator' => Auth::id(),
            'name' => $uploadedFile->hashName(),
            'ext' => $uploadedFile->extension(),
            'size' => $uploadedFile->getSize(),
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
