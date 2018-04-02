<?php
namespace App\myClasses;
use App\myClasses\JunkBlock;

class JunkFunction{
private $startLine;
private $endLine;
private $blocks;
private $blockCount;
private $linesIndicies;
private $numberOfLines;
private $blocksRanges;

public function __construct(){
	$this->blocks = array();
	$this->blockCount = 0;
	$this->numberOfPieces = 0;
	$this->blocksRanges = array();
	$this->linesIndicies = array();
	$this->numberOfLines = 0;
}
public function setStartLine($startLine){
	$this->startLine = $startLine;
}

public function setEndLine($endLine){
	$this->endLine = $endLine;
}
public function getStartLine(){
	return $this->startLine;
}

public function getEndLine(){
	return $this->endLine;
}
public function addBlock(){
	$this->blocks[$this->blockCount] = new JunkBlock;
	$this->blockCount++;
	return $this->blocks[$this->blockCount-1]; 
}
public function returnLastAddedBlock(){
	return $this->blocks[$this->blockCount-1];
}
public function getBlocksRanges(){
//	if(sizeof($this->blocks) ==0)
//		return NULL;
//	else
//	{
		for($i = 0;$i<sizeof($this->blocks);$i++){

			$this->blocksRanges[$i][0] = $this->blocks[$i]->getStartLine();
			$this->blocksRanges[$i][1] = $this->blocks[$i]->getEndLine();
		}
	//}
	return $this->blocksRanges;
}
public function insertLineIndex($index){
	$this->linesIndicies[$this->numberOfLines] = $index;
	$this->numberOfLines++;
}
public function getLinesIndicies(){
	return $this->linesIndicies;
}
public function getNumberOfBlocksAndLines(){
	return (sizeof($this->blocks)+sizeof($this->linesIndicies));
}
public function getBlocksAndLinesIndicies(){
	$getLinesAndBlocks = array();
	for($i = 0;$i<sizeof($this->linesIndicies);$i++)
		array_push($getLinesAndBlocks,$this->linesIndicies[$i]);
	for($i = 0;$i<sizeof($this->blocksRanges);$i++)
		array_push($getLinesAndBlocks,$this->blocksRanges[$i][0]);
	return $getLinesAndBlocks;
}

}

?>