<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class UploadsController extends Controller
{
    public function getUpload(){
    	return view('upload');
    }
 public function postUpload(Request $request) {
        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        // check if the upload is success
        if ($receiver->isUploaded()) {
            // receive the file
            $save = $receiver->receive();
            // check if the upload has finished (in chunk mode it will send smaller files)
            if ($save->isFinished()) {
                // save the file and return any response you need
                return $this->saveFile($save->getFile());
            } else {
                // we are in chunk mode, lets send the current progress
                /** @var AbstractHandler $handler */
                $handler = $save->handler();
                return response()->json([
                    "done" => $handler->getPercentageDone(),
                ]);
            }
        } else {
            throw new UploadMissingFileException();
        }
    }
    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function saveFile(UploadedFile $file)
    {
        $zip = new Zipper;
        $fileName = $file->getClientOriginalName();
     
        $finalPath = storage_path().'/app/upload/';
       
        // move the file name
        $file->move($finalPath, $fileName);
        $zip->make($finalPath.$fileName)->extractTo($finalPath.'/'.pathinfo($fileName, PATHINFO_FILENAME));
        // $exist = Storage::disk('local')->exists('/upload/'.$fileName);
        // if($exist){
        //    Storage::delete('/upload/'.$fileName); 
        // }
       // $output=$this->compileProject();

        
        
        
        return response()->json([
            'path' => $finalPath,
            'name' => $fileName,
            'exist' => $output
        ]);
    }
    protected function compileProject(){
        
    }
    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
   
   
}
