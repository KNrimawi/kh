<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class UploadsController extends Controller
{
    public function getUpload(){
    	return view('upload');
    }
    public function postUpload(request $request){
//        dd(\request()->all());
        $time = Carbon::now();
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            // Getting the extension of the file
            $extension = $image->getClientOriginalExtension();
            // Creating the directory, for example, if the date = 18/10/2017, the directory will be 2017/10/
            $directory = date_format($time, 'Y') . '/' . date_format($time, 'm');
            // Creating the file name: random string followed by the day, random number and the hour
            $filename = str_random(5).date_format($time,'d').rand(1,9).date_format($time,'h').".".$extension;
            // This is our upload main function, storing the image in the storage that named 'public'
            $upload_success = $image->storeAs($directory, $filename, 'public');
            // If the upload is successful, return the name of directory/filename of the upload.
            if ($upload_success) {
//                $uploaded_link = asset(Storage::disk('upload')->url($upload_success));
                return response()->json($upload_success, 200);
            }
            // Else, return error 400
            else {
                return response()->json('error', 400);
            }
        }
        return response()->json('no file to upload', 400);

//        $request->file->storeAs('public',$request->file->getClientOriginalName());
    }
     public function getTest(){
    	 return response()->json(['name'=> 'khaled','age'=>45]);
    	
    	
    }
     public function postTest(request $request){
    	 // return response()->json(['name'=> 'khaled','age'=>45]);
    	// $request->file->store('public');
    	$request->file->store('public');
    	
    }
}
