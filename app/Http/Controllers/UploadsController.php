<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class UploadsController extends Controller
{
    public function getUpload(){
    	return view('upload');
    }
    public function postUpload(request $request){
    	 $request->file->storeAs('public',$request->file->getClientOriginalName());
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
