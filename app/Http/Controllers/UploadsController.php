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
        $JunkCodesFinder = new Finder();
        $forEachNLineInsert = 0;
        $fileName = $file->getClientOriginalName();
        $finalPath = storage_path().'/app/upload/';
        $junkCodesPath= storage_path().'/app/JunkCodes/';
        $JunkCodesDirectories = array();
        $file->move($finalPath, $fileName);
        //$functions = array();
       

        if(strcmp($file->getClientOriginalExtension(),"zip") == 0){ // if it is a zip file

                $this->extractProject($finalPath,$fileName);
                Storage::delete('/upload/'.$fileName); // delete uploaded Zip file
        
                $finder->files()->name('gradlew.bat')->in($finalPath.'/'.pathinfo($fileName, PATHINFO_FILENAME));
              
         
                 foreach ($finder as $file) // find the path of the gradlew
                    $rootPath= $file->getRealPath() ;
                 
                 $JavaFilesFinder->files()->in(str_replace("gradlew.bat","",$rootPath).'\app\src\main\java');
                 $JunkCodesFinder->directories()->in($junkCodesPath);
                    foreach ($JunkCodesFinder as $directory) {
                     array_push($JunkCodesDirectories,$directory->getRealPath());
                    }
                   




                  foreach ($JavaFilesFinder as $file) { //move on java files
                   $BracketsCount = 0;
                   $functions=array();//store functions which junk codes will be added to them
                   $functionsCount = 0;
                   $remainder = 0;
                   $functionLineCount = 0;// number of function lines (a block is considered one line)
                   $addJunkIndex = 0;//the line at which the //addJunk exists
                   $javaFile = array();
                   $forEachLineInsert = 0;
                   $lineCounter = 0;// used for array($javaFile) indexing
                   $filePath = $file->getRealPath();
                   $handle = fopen($file, "r");


                   while (($line = fgets($handle)) !== false){// storing a java file into array

                      if(strpos($line,'{')!==false){
                        $arr = explode("{",$line);//splits at {
                        $javaFile[$lineCounter] = $arr[0];
                        
                        $lineCounter++;
                        $javaFile[$lineCounter] = "{";
                         $lineCounter++;
                        $javaFile[$lineCounter] = $arr[1];
                      }
                      else if(strpos($line,'}')!==false){
                        $arr = explode("}",$line);//splits at }
                        $javaFile[$lineCounter] = $arr[0];
                        $lineCounter++;
                        $javaFile[$lineCounter] = $arr[1];
                        $lineCounter++;
                        $javaFile[$lineCounter] = "}";
                      }
                      else
                        $javaFile[$lineCounter] = $line;
                      
                      if(strpos($line, '//addJunk') !== false){
                        $addJunkIndex = $lineCounter;
                      }

                     
                      $lineCounter++;
                    }

                    

                    
                    // ------- delete unwanted lines
                    $i = $addJunkIndex+1;
                    while($i<$lineCounter){

                        if(strpos($javaFile[$i],'}') === false&&
                         strpos($javaFile[$i],'{') === false&&
                         strpos($javaFile[$i],'(') === false&&
                         strpos($javaFile[$i],')') === false&&
                         strpos($javaFile[$i],';') === false&&
                         !preg_match("/[a-zA-Z]/i", $javaFile[$i])){
                          array_splice($javaFile, $i, 1);
                          $lineCounter--;
                      }
                      else
                        $i++;

                    }
                   
                    for($i = $addJunkIndex+1; $i<$lineCounter; $i++){//moving on java file from the
                      // line that is next to the addJunk comment

                      if(strpos($javaFile[$i], '{') !== false){
                       
                        $BracketsCount++;
                        if($BracketsCount == 1){ //it's a function start
                          $functions[$functionsCount] = new JunkFunction;
                          $functions[$functionsCount]->setStartLine($i);
                          $functionsCount++;
                          
                          
                        }
                        else if($BracketsCount>1){ // it's a block start
                           $functions[$functionsCount-1]->addBlock()->setStartLine($i-1);
                          
                         
                        }

                        
                      }
                      else if(strpos($javaFile[$i], '}') !== false){
                        $BracketsCount --;
                        if($BracketsCount == 0){ //it's a function end

                          $functions[$functionsCount-1]->setEndLine($i);
                        }
                        else if($BracketsCount >0){ // it's a block end

                          $functions[$functionsCount-1]->returnLastAddedBlock()->setEndLine($i);
                        
                        }

                      }
                     
                    }
                      $blocksRanges = $functions[$functionsCount-1]->getBlocksRanges(); 
                      //get indicies of the lines that contains code
                    for($i = $functions[$functionsCount-1]->getStartLine()+1;$i<$functions[$functionsCount-1]->getEndLine();$i++){

                      $InsideBlock = false;
                      for($j = 0;$j<sizeof($blocksRanges);$j++){

                          if($i>=$blocksRanges[$j][0]&&$i<=$blocksRanges[$j][1])
                            $InsideBlock = true;
                      }
                      if(!$InsideBlock&&preg_match("/[a-zA-Z]/i", $javaFile[$i]))
                        $functions[$functionsCount-1]->insertLineIndex($i);

                    }
                    
                   
                
                   $numberOfLinesandBlocks = $functions[$functionsCount-1]->getNumberOfBlocksAndLines();
                   $junkCodePieces = array();
                   $counter = 0;//to trace the original index of lines and blocks
                   $chosenJunkCode = $JunkCodesDirectories[0];
                   $junkCodePiecesFinder = new Finder();
                   $junkCodePiecesFinder->files()->in($chosenJunkCode);

                   foreach($junkCodePiecesFinder as $piece){
                    array_push($junkCodePieces,$piece->getRealPath());
                   }
                   
                  
                   if(sizeof($junkCodePiecesFinder)>$numberOfLinesandBlocks){// for each line or block insert N

                    $forEachLineInsert = intval(sizeof($junkCodePieces)/$numberOfLinesandBlocks);
                    $remainder = sizeof($junkCodePieces)%$numberOfLinesandBlocks;
                    $blocksAndLinesIndicies = $functions[$functionsCount-1]->getBlocksAndLinesIndicies();
                    $piecesDone = 0;
                    sort($blocksAndLinesIndicies);

                    for($i = 0;$i<$numberOfLinesandBlocks;$i++){
                      for($j = 0;$j<$forEachLineInsert;$j++){
                        if($piecesDone < sizeof($junkCodePieces)){
                          array_splice( $javaFile, $blocksAndLinesIndicies[$i]+$counter, 0, File::get( $junkCodePieces[$piecesDone]));
                    
                          $counter++;
                        }
                        
                        else
                          break;
                        $piecesDone++;
                      }
                      
                    }
                    
                    for($j=0;$j<$remainder;$j++){
                        array_splice( $javaFile, $blocksAndLinesIndicies[sizeof($blocksAndLinesIndicies)-1]+$counter, 0, File::get( $junkCodePieces[sizeof($junkCodePieces)-$remainder+$j]));
                       
                        $counter++;
                      }
                   } 
                   // else{
                   //  $forEachNLineInsert = $numberOfLinesandBlocks / $JunkCodesDirectories;
                   //  $remainder = $numberOfLinesandBlocks % $JunkCodesDirectories;
                   // }
                   
                   file_put_contents($filePath, '');
                   foreach($javaFile as $line){
                    file_put_contents($filePath, $line , FILE_APPEND);
                   }


                    
                  }
                

                  
                  


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
