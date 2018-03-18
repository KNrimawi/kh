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
#-------------------------------------------------------------------------------------------------------------
    protected function saveFile(UploadedFile $file)
    {
        $rootPath=NULL;
        $finder = new Finder();
        $fileName = $file->getClientOriginalName();
        $finalPath = storage_path().'/app/upload/';
        $file->move($finalPath, $fileName);

        if(strcmp($file->getClientOriginalExtension(),"zip") == 0){ // if it is a zip file

                $this->extractProject($finalPath,$fileName);
                Storage::delete('/upload/'.$fileName); // delete uploaded Zip file
        
                $finder->files()->name('gradlew.bat')->in($finalPath.'/'.pathinfo($fileName, PATHINFO_FILENAME));
         
                 foreach ($finder as $file) // find the path of the gradlew
                    $rootPath= $file->getRealPath() ;
                 

                 if($rootPath != NULL) //compiling the project
                    return $this->compileProject($rootPath);
                 
                 else{ // if it is not an Android project
                    return response()->json([
                     'status' => 'Afalse'
                    ]);
                 }
        }
        else{ // if it is not a zip file

            Storage::delete('/upload/'.$fileName);
            return response()->json([
                     'status' => 'Zfalse'
                    ]);
        }
        }
 #-------------------------------------------------------------------------------------------------------------
     protected function extractProject($finalPath,$fileName){
        $zip = new Zipper;
        $zip->make($finalPath.$fileName)->extractTo($finalPath.'/'.pathinfo($fileName, PATHINFO_FILENAME));
        $zip->close();
    }
 #-------------------------------------------------------------------------------------------------------------   

    protected function compileProject($rootPath){

        $rootPath=str_replace("gradlew.bat","",$rootPath);
        $pathToLocalProperties=str_replace(storage_path().'\app',"",$rootPath.'/local.properties');
        $pathToGradleBuild =$rootPath.'/app/build.gradle';
        Storage::delete($pathToLocalProperties);
        File::copy(storage_path().'\app\for_SDK\local.properties', $rootPath.'/local.properties');

        $this->applyProguard($pathToGradleBuild);           
        chdir($rootPath);
        exec('gradlew assembleDebug'); 
        return response()->json([
               'status'=>'success'
        ]);
    }

#-----------------------------------------------------------------------------------------------------
        protected function applyProguard($pathToGradleBuild){
        $gradleBuildContent = array();
        $lineCounter = 0;
        $handle = fopen($pathToGradleBuild, "r");
                    if ($handle) {
                        while (($line = fgets($handle)) !== false) {

                            if(strpos($line, 'buildTypes') !== false){
                                  $gradleBuildContent[$lineCounter] = $line;
                                  $lineCounter++;
                                  $gradleBuildContent[$lineCounter] ="debug {\nminifyEnabled true\nproguardFiles getDefaultProguardFile('proguard-android.txt'), 'proguard-rules.pro'\n}\n" ;
                            }
                           
                            
                            else
                                $gradleBuildContent[$lineCounter] = $line;
                            $lineCounter++;
                        }

                        fclose($handle);
                        file_put_contents($pathToGradleBuild, $gradleBuildContent);
                        
                    } else {
                        // error opening the file.
                    }
    }
    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
   
   
}
