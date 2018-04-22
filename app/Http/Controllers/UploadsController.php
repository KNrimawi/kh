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
use App\myClasses\DebugDetectionComments;
/*Things to be done
*define blocks with out brackets (if,while...)
*ex:
 * if()
 * x=10;

*/


class UploadsController extends Controller
{
    public function getUpload()
    {
        return view('upload');
    }

    public function postUpload(Request $request)
    {
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
        $rootPath = NULL;
        $finder = new Finder();
        $fileName = $file->getClientOriginalName();
        $finalPath = storage_path() . '/app/upload/';
        $file->move($finalPath, $fileName);//save uploaded file


        if (strcmp($file->getClientOriginalExtension(), "zip") == 0) { // if it is a zip file

            $this->extractProject($finalPath, $fileName);
            Storage::delete('/upload/' . $fileName); // delete uploaded Zip file

            $finder->files()->name('gradlew.bat')->in($finalPath . '/' . pathinfo($fileName, PATHINFO_FILENAME));


            foreach ($finder as $file) // find the path of the gradlew
                $rootPath = $file->getRealPath();

            if ($rootPath != NULL) //compiling the project
            {
                $this->addReverseEngineeringLibrary($rootPath);
                $this->insertAntiReverseMethods($rootPath);
                $this->addJunks($rootPath);
                return $this->compileProject($rootPath);
            } else { // if it is not an Android project
                return response()->json([
                    'status' => 'Afalse'
                ]);
            }
        } else { // if it is not a zip file

            Storage::delete('/upload/' . $fileName);
            return response()->json([
                'status' => 'Zfalse'
            ]);
        }
    }

    #-------------------------------------------------------------------------------------------------------------
    protected function extractProject($finalPath, $fileName)
    {
        $zip = new Zipper;
        $zip->make($finalPath . $fileName)->extractTo($finalPath . '/' . pathinfo($fileName, PATHINFO_FILENAME));
        $zip->close();
    }

    #-------------------------------------------------------------------------------------------------------------

    protected function compileProject($rootPath)
    {

        $rootPath = str_replace("gradlew.bat", "", $rootPath);
        $pathToLocalProperties = str_replace(storage_path() . '\app', "", $rootPath . '/local.properties');
        $pathToGradleBuild = $rootPath . '/app/build.gradle';
        Storage::delete($pathToLocalProperties);
        File::copy(storage_path() . '\app\for_SDK\local.properties', $rootPath . '/local.properties');

        $this->applyProguard($pathToGradleBuild);
        chdir($rootPath);
        exec('gradlew assembleDebug');
        return response()->json([
            'status' => 'success'
        ]);
    }

#-----------------------------------------------------------------------------------------------------
    protected function applyProguard($pathToGradleBuild)
    {
        $gradleBuildContent = array();
        $lineCounter = 0;
        $handle = fopen($pathToGradleBuild, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {

                if (strpos(strtolower($line), 'buildtypes') !== false) {
                    $gradleBuildContent[$lineCounter] = $line;
                    $lineCounter++;
                    $gradleBuildContent[$lineCounter] = "debug {\nminifyEnabled true\nproguardFiles getDefaultProguardFile('proguard-android.txt'), 'proguard-rules.pro'\n}\n";
                } else
                    $gradleBuildContent[$lineCounter] = $line;
                $lineCounter++;
            }

            fclose($handle);
            file_put_contents($pathToGradleBuild, $gradleBuildContent);

        } else {
            // error opening the file.
        }
    }

#-------------------------------------------------------------------------------------------------------
    protected function addJunks($rootPath)
    {

        $JavaFilesFinder = new Finder();
        $JunkCodesFinder = new Finder();
        $junkCodesPath = storage_path() . '/app/JunkCodes/';
        $JunkCodesDirectories = array();
        $filesPath = array();
        $JavaFilesFinder->files()->in(str_replace("gradlew.bat", "", $rootPath) . '\app\src\main\java');//uploaded project files
        $JunkCodesFinder->directories()->in($junkCodesPath);//junk codes store on the server

        foreach ($JunkCodesFinder as $directory) {

            array_push($JunkCodesDirectories, $directory->getRealPath());
        }


        foreach ($JavaFilesFinder as $file) { //move on java files

            $toAppend = "";
            $functions = array();//store functions which junk codes will be added to them
            $functionsCount = 0;
            $addJunkIndex = array();//the line at which the //addJunk exists
            $javaFile = array();
            $lineCounter = 0;// used for array($javaFile) indexing
            $filePath = $file->getRealPath();
            $handle = fopen($file, "r");


            while (($line = fgets($handle)) !== false) {// storing a java file into array

                if (strpos($line, '{') !== false) {
                    $arr = explode("{", $line);//splits at {
                    for ($i = 0; $i < count($arr); $i++) {

                        $javaFile[$lineCounter] = $arr[$i];
                        $lineCounter++;
                        if ($i + 1 != count($arr)) {
                            $javaFile[$lineCounter] = "{";
                            $lineCounter++;
                        }

                    }

                } else if (strpos($line, '}') !== false) {

                    $arr = explode("}", $line);//splits at }

                    for ($i = 0; $i < count($arr); $i++) {

                        $javaFile[$lineCounter] = $arr[$i];
                        $lineCounter++;
                        if ($i + 1 != count($arr)) {
                            $javaFile[$lineCounter] = "}";
                            $lineCounter++;
                        }

                    }


                } else if (strpos($line, ';') !== false) {
                    if ($toAppend != "") {

                        $javaFile[$lineCounter] = $toAppend . $line;
                        $toAppend = "";
                    } else
                        $javaFile[$lineCounter] = $line;
                    $lineCounter++;
                } else if (strpos($line, '//') !== false) {

                    $javaFile[$lineCounter] = $line;
                    $lineCounter++;

                } else {
                    $line = trim($line, " \n\t\r");
                    if (!(strpos($line, '@') !== false && strpos($line, '@') == 0) &&
                        strpos(strtolower($line), "public") === false &&
                        strpos(strtolower($line), "private") === false &&
                        strpos(strtolower($line), "protected") === false&&
                        strpos(strtolower($line), "while") === false&&
                        strpos(strtolower($line), "for") === false&&
                        strpos(strtolower($line), "if") === false&&
                        strpos(strtolower($line), "switch") === false&&
                        strpos(strtolower($line), "else") === false

                    ) {
                        $toAppend .= $line;
                    } else {
                        $javaFile[$lineCounter] = $line;
                        $lineCounter++;
                    }
                }


            }
            if (strpos($file->getRealPath(), "AntiReverse") === false)
                Log::info($javaFile);


            $i = 0;
            while ($i < $lineCounter) {

                if (strpos($javaFile[$i], '}') === false &&
                    strpos($javaFile[$i], '{') === false &&
                    strpos($javaFile[$i], '(') === false &&
                    strpos($javaFile[$i], ')') === false &&
                    strpos($javaFile[$i], ';') === false &&
                    !preg_match("/[a-zA-Z]/i", $javaFile[$i])) {
                    array_splice($javaFile, $i, 1);
                    $lineCounter--;
                } else
                    $i++;

            }

            for ($i = 0; $i < count($javaFile); $i++) {
                if (strpos(strtolower($javaFile[$i]), '//addjunk') !== false) {

                    array_push($addJunkIndex, $i);
                }
            }


            if (count($addJunkIndex) != 0) {


                for ($index = 0; $index < count($addJunkIndex); $index++) {//move on //addjunk indecies

                    $BracketsCount = 0;
                    $endOfFunction = false;
                    $ind = $addJunkIndex[$index] + 1;
                    while (!$endOfFunction) {

                        if (strpos($javaFile[$ind], '{') !== false) {

                            $BracketsCount++;

                            if ($BracketsCount == 1) { //it's a function start
                                $functions[$functionsCount] = new JunkFunction;
                                $functions[$functionsCount]->setStartLine($ind);
                                $functionsCount++;
                            } else if ($BracketsCount > 1) { // it's a block start
                                $functions[$functionsCount - 1]->addBlock()->setStartLine($ind - 1);
                            }
                        } else if (strpos($javaFile[$ind], '}') !== false) {
                            $BracketsCount--;
                            if ($BracketsCount == 0) { //it's a function end

                                $functions[$functionsCount - 1]->setEndLine($ind);
                                $endOfFunction = true;
                            } else if ($BracketsCount > 0) { // it's a block end

                                $functions[$functionsCount - 1]->returnLastAddedBlock()->setEndLine($ind);

                            }

                        }
                        $ind++;
                    }


                    $blocksRanges = $functions[$functionsCount - 1]->getBlocksRanges();
                    //get indicies of the lines that contains code
                    for ($i = $functions[$functionsCount - 1]->getStartLine() + 1; $i < $functions[$functionsCount - 1]->getEndLine(); $i++) {

                        $InsideBlock = false;

                        for ($j = 0; $j < count($blocksRanges); $j++) {

                            if ($i >= $blocksRanges[$j][0] && $i <= $blocksRanges[$j][1])
                                $InsideBlock = true;
                        }

                        if (!$InsideBlock && preg_match("/[a-zA-Z]/i", $javaFile[$i]))
                            $functions[$functionsCount - 1]->insertLineIndex($i);

                    }


                    $numberOfLinesandBlocks = $functions[$functionsCount - 1]->getNumberOfBlocksAndLines();
                    $junkCodePieces = array();
                    $counter = 0;//to trace the original index of lines and blocks
                    $junkCodePiecesFinder = new Finder();
                    $junkCodePiecesFinder->files()->in($JunkCodesDirectories[0]);
                    /**change the index of the array to random**/

                    foreach ($junkCodePiecesFinder as $piece) {//saving junk code pieces
                        array_push($junkCodePieces, $piece->getRealPath());
                    }


                    if (count($junkCodePiecesFinder) > $numberOfLinesandBlocks) {// for each line or block insert N

                        $forEachLineInsert = intval(count($junkCodePieces) / $numberOfLinesandBlocks);
                        $remainder = count($junkCodePieces) % $numberOfLinesandBlocks;
                        $blocksAndLinesIndicies = $functions[$functionsCount - 1]->getBlocksAndLinesIndicies();
                        $piecesDone = 0;
                        sort($blocksAndLinesIndicies);

                        for ($i = 0; $i < $numberOfLinesandBlocks; $i++) {

                            for ($j = 0; $j < $forEachLineInsert; $j++) {

                                if ($piecesDone < count($junkCodePieces)) {

                                    array_splice($javaFile, $blocksAndLinesIndicies[$i] + $counter, 0, File::get($junkCodePieces[$piecesDone]));
                                    $counter++;
                                } else
                                    break;

                                $piecesDone++;
                            }

                        }

                        for ($j = 0; $j < $remainder; $j++) {
                            array_splice($javaFile, $blocksAndLinesIndicies[count($blocksAndLinesIndicies) - 1] + $counter, 0, File::get($junkCodePieces[sizeof($junkCodePieces) - $remainder + $j]));
                            $counter++;
                        }
                    }
                    else{ //  # of lines in $junkCodePieces  < $numberOfLinesandBlocks

                        $forEachLineInsert = intval( $numberOfLinesandBlocks /count($junkCodePieces));
                        $remainder = $numberOfLinesandBlocks % count($junkCodePieces);
                        $blocksAndLinesIndicies = $functions[$functionsCount - 1]->getBlocksAndLinesIndicies();
                        $piecesDone = 0;

                        sort($blocksAndLinesIndicies);

                        for ($i = 0; $i < count($junkCodePieces); $i++) {

                            for ($j = 0; $j < $forEachLineInsert; $j++) {

                                if ($piecesDone < $numberOfLinesandBlocks) {

                                    array_splice($javaFile, $blocksAndLinesIndicies[$i] + $counter, 0, File::get($junkCodePieces[$piecesDone]));
                                    $counter++;
                                } else
                                    break;

                                $piecesDone++;
                            }

                        }

                        for ($j = 0; $j < $remainder; $j++) {
                            array_splice($javaFile, $blocksAndLinesIndicies[count($blocksAndLinesIndicies) - 1] + $counter, 0, File::get($junkCodePieces[sizeof($junkCodePieces) - $remainder + $j]));
                            $counter++;
                        }


                    }
                    for ($k = $index + 1; $k < count($addJunkIndex); $k++)
                        $addJunkIndex[$k] += $counter;


                }
                file_put_contents($filePath, '');

                foreach ($javaFile as $line) {
                    file_put_contents($filePath, $line, FILE_APPEND);
                }

            }


        }


    }

    protected function addReverseEngineeringLibrary($rootPath)
    {

        $relativePath = NULL;
        $JavaFilesFinder = new Finder();
        $libraryPath = storage_path() . '/app/Library/AntiReverseEngineeringClass.java';


        $JavaFilesFinder->files()->in(str_replace("gradlew.bat", "", $rootPath) . '\app\src\main\java');
        foreach ($JavaFilesFinder as $file)
            $relativePath = $file->getRelativePath();

        File::copy($libraryPath, str_replace("gradlew.bat", "", $rootPath) . "\app\src\main\java\\" . $relativePath . '\AntiReverseEngineeringClass.java');

        $contents = File::get(str_replace("gradlew.bat", "", $rootPath) . "\app\src\main\java\\" . $relativePath . '\AntiReverseEngineeringClass.java');
        $contents = "package " . str_replace('\\', '.', $relativePath) . ";\n" . $contents;
        file_put_contents(str_replace("gradlew.bat", "", $rootPath) . "\app\src\main\java\\" . $relativePath . '\AntiReverseEngineeringClass.java', $contents);

    }

    protected function insertAntiReverseMethods($rootPath)
    {
        $JavaFilesFinder = new Finder();

        $rootMethods = array("AntiReverseEngineeringClass.checkRootMethod1(true)",
            "AntiReverseEngineeringClass.checkRootMethod2(true)",
            "AntiReverseEngineeringClass
            .checkRootMethod3(true)");

        $debugMethodTerminate = array("AntiReverseEngineeringClass.detectDebugging1(true)",
            "AntiReverseEngineeringClass.detectDebugging2(true)");

        $debugMethodNoTerminate = array("AntiReverseEngineeringClass.detectDebugging1(false)",
            "AntiReverseEngineeringClass.detectDebugging2(false)");
        $JavaFilesFinder->files()->in(str_replace("gradlew.bat", "", $rootPath) . '\app\src\main\java');
        foreach ($JavaFilesFinder as $file) { // loop on each java file except the library file
            if (strpos($file->getRealPath(), "AntiReverseEngineeringClass") === false) {
                #-------- for debugging methods -------------
                $posOfComment = 0;
                $comments = array();
                $commentString = "";
                $fileContent = File::get($file->getRealPath());
                while (($posOfComment = strpos(strtolower($fileContent), "//adddebugdetection", $posOfComment)) !== false) {
                    $comment = new DebugDetectionComments();
                    $comment->setStartPosition($posOfComment);
                    $commentString .= $fileContent[$posOfComment];
                    $posOfComment++;
                    $closingBracket = false;
                    while ($closingBracket != true) {

                        if ($fileContent[$posOfComment] == ']') {
                            $comment->setEndPosition($posOfComment);
                            $closingBracket = true;

                        }
                        $commentString .= $fileContent[$posOfComment];


                        $posOfComment++;
                    }
                    $comment->setComment($commentString);
                    $comments[] = $comment;
                    $commentString = "";

                }
                //Log::info($comments);

                $replacementLength = 0;
                for ($i = 0; $i < count($comments); $i++) {

                    preg_match("/\[(.*)\]/", $comments[$i]->getComment(), $matches);

                    if (strlen(trim($matches[1])) == 0) {
                        $replacement = $debugMethodTerminate[rand(0, 1)] . ";\n";


                        $fileContent = substr_replace($fileContent, $replacement, $comments[$i]->getStartPosition(), strlen($comments[$i]->getComment()));
                        $replacementLength += strlen($replacement);
                        if ($i + 1 != sizeof($comments))
                            $comments[$i + 1]->setStartPosition($comments[$i + 1]->getStartPosition() + $replacementLength - strlen($comments[$i]->getComment()));


                    } else {
                        $replacement = "if(" . $debugMethodNoTerminate[rand(0, 1)] . "){\n";//tell muath
                        $replacement .= $matches[1] . ";}\n";//tell muath

                        $fileContent = substr_replace($fileContent, $replacement, $comments[$i]->getStartPosition(), strlen($comments[$i]->getComment()));
                        $replacementLength += strlen($replacement);

                        if ($i + 1 != sizeof($comments))
                            $comments[$i + 1]->setStartPosition($comments[$i + 1]->getStartPosition() + $replacementLength - strlen($comments[$i]->getComment()));

                    }

                }

                file_put_contents($file->getRealPath(), $fileContent);

                #-------------end of inserting debugging methods and start of inserting root methods---------------
                $fileContent = File::get($file->getRealPath());
                $arr = explode("\n", $fileContent);
                for ($i = 0; $i < sizeof($arr); $i++) {
                    if (strpos(strtolower($arr[$i]), "oncreate", 0) !== false) {
                        array_splice($arr, $i + 1, 0, $rootMethods[rand(0, 2)] . ";\n");
                        break;

                    }


                }

                file_put_contents($file->getRealPath(), '');
                $content = '';
                foreach ($arr as $line) {
                    $content = $content . $line . "\n";
                }
                file_put_contents($file->getRealPath(), $content);


            }


        }


    }


}

