<?php
namespace PhpDatabaseAnalyzer;

class Logger implements LoggerInterface
{

    private $loggingMode;

    private $log = array();

    private $scoreMultiplicator = array(
        'notice' => 1,
        'warning' => 5,
        'critical' => 10
    );

    public function __construct($loggingMode)
    {
        if ($loggingMode == 'issues') {
            $this->loggingMode = $loggingMode;
        } else {
            $this->loggingMode = "full";
        }
    }

    public function setInfo($text)
    {
        if ($this->loggingMode == 'full' and is_string($text) and ! empty($text)) {
            $this->log[] = array(
                'logType' => 'info',
                'timestamp' => date("U"),
                'text' => $text
            );
            return true;
        } else {
            return false;
        }
    }

    public function setIssue($type, $text, $scorePoints)
    {
        if (! empty($text) and preg_match("(notice|warning|critical)", $type) and $scorePoints > 0) {
            $this->log[] = array(
                'logType' => 'issue',
                'timestamp' => date("U"),
                'issueType' => $type,
                'text' => $text,
                'scorePoints' => $scorePoints
            );
            return true;
        } else {
            return false;
        }
    }

    public function getLog()
    {
        return $this->log;
    }

    public function getIssues()
    {
        $issues = array();
        
        foreach ($this->log as $logEntry) {
            if ($logEntry['logType'] == 'issue') {
                unset($logEntry['logType']);
                $issues[] = $logEntry;
            }
        }
        
        return $issues;
    }

    public function getQualityScore()
    {
        $qualityScore = 0;
        
        foreach ($this->log as $logEntry) {
            if ($logEntry['logType'] == 'issue') {
                $qualityScore += $this->scoreMultiplicator[$logEntry['issueType']] * $logEntry['scorePoints'];
            }
        }
        
        return $qualityScore;
    }
}
