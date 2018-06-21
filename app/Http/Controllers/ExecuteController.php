<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class ExecuteController extends Controller
{
    protected function getExecute(){
        return view("test");
    }
    public function executeFunction(Request $request){


        Log::info($request->getContent());


              $path = storage_path() . '/app/functions/';
              $data = json_decode($request->getContent());
              $fileName = $data->fileName;


            chdir("D:\graduation project\kh\storage\app\\functions");
            exec('set "path=%path%;C:\Program Files\Java\jdk1.8.0_161\bin" 2>&1&&java -cp .;org.json.jar '.str_replace(".java","",$fileName).' '.str_replace('"','\"',$request->getContent()).' 2>&1',$res);

            return $res[0];




    }
}
