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
use App\myClasses\JunkFunction;

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
        $JavaFilesFinder = new Finder();
        $fileName = $file->getClientOriginalName();
        $finalPath = storage_path().'/app/upload/';
        $junkCodesPath= storage_path().'/app/JunkCodes/';
        $file->move($finalPath, $fileName);
        //$functions = array();
       

        if(strcmp($file->getClientOriginalExtension(),"zip") == 0){ // if it is a zip file

                $this->extractProject($finalPath,$fileName);
                Storage::delete('/upload/'.$fileName); // delete uploaded Zip file
        
                $finder->files()->name('gradlew.bat')->in($finalPath.'/'.pathinfo($fileName, PATHINFO_FILENAME));
              
         
                 foreach ($finder as $file) // find the path of the gradlew
                    $rootPath= $file->getRealPath() ;
                 
                 $JavaFilesFinder->files()->in(str_replace("gradlew.bat","",$rootPath).'\app\src\main\java');
                    // $functions[0] = new JunkFunction;
                    // $functions[0]->addBlock()->setStartLine(1);
                    // $functions[0]->returnLastAddedBlock();



                  foreach ($JavaFilesFinder as $file) { //move on java files
                   $BracketsCount = 0;
                   $functions=array();//store functions
                   $functionsCount = 0;
                   $InsideBlock = false;
                   $functionLineCount = 0;// number of function lines (a block is considered one line)
                   $addJunkIndex = 0;//the line at which the //addJunk exists
                   $javaFile = array();
                   $lineCounter = 0;// used for array($javaFile) indexing
                   $handle = fopen($file, "r");

                   while (($line = fgets($handle)) !== false){// storing a java file into array

                      if(strpos($line,'{')!==false){
                        $arr = explode("{",$line);//splits at {
                        $javaFile[0][$lineCounter] = $arr[0]."{";
                        $javaFile[1][$lineCounter] = "";
                        $lineCounter++;
                        $javaFile[0][$lineCounter] = $arr[1];
                      }
                      else if(strpos($line,'}')!==false){
                        $arr = explode("}",$line);//splits at }
                        $javaFile[0][$lineCounter] = $arr[0];
                        $javaFile[1][$lineCounter] = "";
                        $lineCounter++;
                        $javaFile[0][$lineCounter] = $arr[1]."}";
                      }
                      else
                        $javaFile[0][$lineCounter] = $line;
                      
                      if(strpos($line, '//addJunk') !== false){
                        $addJunkIndex = $lineCounter;
                      }

                      $javaFile[1][$lineCounter] = ""; // this will be used later for checking if a line is a 
                      //start of function, block or end of function and block
                      $lineCounter++;
                    }

                    for($i = $addJunkIndex+1; $i<$lineCounter; $i++){//moving on java file from the
                      // line that is next to the addJunk comment

                      if(strpos($javaFile[0][$i], '{') !== false){
                       
                        $BracketsCount++;
                        if($BracketsCount == 1){ //it's a function start
                          $functions[$functionsCount] = new JunkFunction;
                          $functions[$functionsCount]->setStartLine($i);
                          $functionsCount++;
                          
                          
                        }
                        else if($BracketsCount>1){ // it's a block start
                           $functions[$functionsCount-1]->addBlock()->setStartLine($i);
                          
                         
                        }

                        
                      }
                      else if(strpos($javaFile[0][$i], '}') !== false){
                        $BracketsCount --;
                        if($BracketsCount == 0){ //it's a function end

                          $functions[$functionsCount-1]->setEndLine($i);
                        }
                        else if($BracketsCount >0){ // it's a block end

                          $functions[$functionsCount-1]->returnLastAddedBlock()->setEndLine($i);
                        
                        }

                      }
                     

                      // if(preg_match("/[a-zA-Z]/i", $javaFile[0][$i])){ // check if a line is a code or no
                      //   $javaFile[2][$i] = "code";
                      //   if($InsideBlock == false)
                      //     $functionLineCount++;

                      // }
                      // else{
                      //   if($javaFile[1][$i] == "BE")
                      //     $functionLineCount++;

                      //   $javaFile[2][$i] = "no code";

                      // }
                    }


                   


                  }
                Log::info($functions);
                Log::info($javaFile);

                  
                  


                 // if($rootPath != NULL) //compiling the project
                 //    return $this->compileProject($rootPath);
                 
                 // else{ // if it is not an Android project
                    return response()->json([
                     'status' => 'Afalse'
                    ]);
                 // }
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

        //$this->applyProguard($pathToGradleBuild);           
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
