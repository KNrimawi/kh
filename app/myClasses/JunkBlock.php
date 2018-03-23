<?php
namespace App\myClasses;
class JunkBlock{
private $startLine;
private $endLine;


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
}
?>