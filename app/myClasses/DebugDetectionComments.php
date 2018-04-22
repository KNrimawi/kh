<?php
namespace App\myClasses;
class DebugDetectionComments{
    private $startPosition;
    private $endPosition;
    private $comment;

    /**
     * @return mixed
     */
    public function getEndPosition()
    {
        return $this->endPosition;
    }

    /**
     * @param mixed $endPosition
     */
    public function setEndPosition($endPosition)
    {
        $this->endPosition = $endPosition;
    }


    /**
     * @return mixed
     */
    public function getStartPosition()
    {
        return $this->startPosition;
    }


    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
 * @param mixed $startPosition
 */
    public function setStartPosition($startPosition)
    {
        $this->startPosition = $startPosition;
    }

}
?>
