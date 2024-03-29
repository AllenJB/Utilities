<?php
declare(strict_types = 1);

namespace AllenJB\Utilities;

/**
 * Utility class to aid reporting of iteration tasks (speed / progress)
 */
class IteratorGauge
{

    protected $lineCount = null;

    protected $lineNo = -1;

    protected $tsStart = null;


    public function __construct($firstLineNo = 0)
    {
        $this->lineNo = $firstLineNo - 1;
        $this->tsStart = time();
    }


    public function incrementLineNo($by = 1) : int
    {
        $this->lineNo += $by;

        return $this->lineNo;
    }


    public function setLineCount($count) : void
    {
        $this->lineCount = $count;
    }


    public function getLineNo() : int
    {
        return $this->lineNo;
    }


    public function getLineCount() : ?int
    {
        return $this->lineCount;
    }


    public function getPercentage($dp = 2) : string
    {
        if ($this->lineCount < 1) {
            return number_format(0, $dp) . '%';
        }
        return number_format(($this->lineNo / $this->lineCount) * 100, $dp) . '%';
    }


    public function getSpeed() : string
    {
        $elapsed = time() - $this->tsStart;
        $lpm = (($elapsed > 0) ? ($this->lineNo / $elapsed) * 60 : 0);
        return number_format($lpm, 0) . ' rpm';
    }


    public function getElapsedTime($includeSeconds = false) : string
    {
        $elapsed = time() - $this->tsStart;
        $secs = $elapsed % 60;
        $secs = str_pad("". $secs, 2, '0', STR_PAD_LEFT);
        $mins = floor(($elapsed / 60) % 60);
        $mins = str_pad("". $mins, 2, '0', STR_PAD_LEFT);
        $hours = floor($elapsed / 3600);
        return "{$hours}h {$mins}m" . ($includeSeconds ? " {$secs}s" : '');
    }


    public function getRemainingTime() : string
    {
        if ($this->lineCount < 1) {
            return "unknown";
        }

        $elapsed = time() - $this->tsStart;
        $linesRemaining = $this->lineCount - $this->lineNo;

        $lpm = 0;
        if ($elapsed > 0) {
            $lpm = floor(($this->lineNo / $elapsed) * 60);
        }

        $timeRemaining = 0;
        if ($lpm > 0) {
            $timeRemaining = $linesRemaining / $lpm;
        }

        $hours = floor($timeRemaining / 60);
        $mins = $timeRemaining % 60;
        $mins = str_pad("". $mins, 2, '0', STR_PAD_LEFT);

        return "{$hours}h {$mins}m";
    }


    /**
     * Display the current line progress and percentage completion
     *
     * @return string
     */
    public function getProgressText() : string
    {
        if ($this->lineCount < 1) {
            return number_format($this->getLineNo()) . ' / ?';
        }
        return number_format($this->getLineNo()) . ' / '
            . number_format($this->getLineCount()) . " = {$this->getPercentage()}";
    }


    /**
     * Display the speed and estimated remaining time
     *
     * @return string
     */
    public function getStatusText() : string
    {
        return "Time: {$this->getElapsedTime()} :: Speed: "
            . $this->getSpeed() ." :: ETC: {$this->getRemainingTime()}";
    }

}
