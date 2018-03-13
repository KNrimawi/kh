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
use Symfony\Component\Finder\Finder;
use Log;

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
        $gradlepath=NULL;
        $success=false;
        $finder = new Finder();
        $fileName = $file->getClientOriginalName();
        $finalPath = storage_path().'/app/upload/';
        $file->move($finalPath, $fileName);

        $this->extractProject($finalPath,$fileName);
        
        Storage::delete('/upload/'.$fileName); // delete uploaded Zip file
        
        
         $finder->files()->name('gradlew.bat')->in($finalPath.'/'.pathinfo($fileName, PATHINFO_FILENAME));
         
         foreach ($finder as $file) {
            $gradlepath= $file->getRealPath() ;
         }

         if($gradlepath != NULL){//compiling

            $gradlepath=str_replace("gradlew.bat","",$gradlepath);
            $pathToLocalProperties=str_replace(storage_path().'\app',"",$gradlepath.'/local.properties');
            Storage::delete($pathToLocalProperties);
            File::copy(storage_path().'\app\for_SDK\local.properties', $gradlepath.'/local.properties');
            $success=chdir($gradlepath);
            $out = exec('gradlew assembleDebug');

         }

        return response()->json([
            'path' => $pathToLocalProperties,
            
        ]);
    }


     protected function extractProject($finalPath,$fileName){
        $zip = new Zipper;
        $zip->make($finalPath.$fileName)->extractTo($finalPath.'/'.pathinfo($fileName, PATHINFO_FILENAME));
        $zip->close();
    }
    protected function compileProject(){
        
    }
    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
   
   
}
