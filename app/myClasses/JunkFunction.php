<?php
namespace App\myClasses;
use App\myClasses\JunkBlock;

class JunkFunction{
private $startLine;
private $endLine;
private $blocks;
private $blockCount;
public function __construct(){
	$this->blocks = array();
	$this->blockCount = 0;
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

}

?>