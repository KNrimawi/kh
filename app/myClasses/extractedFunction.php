<?php
namespace App\myClasses;
class extractedFunction{
  private $arguments;
  private $numOfArguments;
    function __construct() {
        $this->arguments = array();
        $this->numOfArguments=0;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     */
    public function setArguments($argumentName,$argumentType)
    {
        $this->arguments[$this->numOfArguments][0]=$argumentName;
        $this->arguments[$this->numOfArguments][1]=$argumentType;
        $this->numOfArguments++;
    }

}
?>