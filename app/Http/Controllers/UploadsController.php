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
    	 return Response::json(['name'=> 'khaled','age'=>45]);
    }
     public function getTest(){
    	 return response()->json(['name'=> 'khaled','age'=>45]);
    	 $this->info('Display this on the screen');
    	
    }
}
