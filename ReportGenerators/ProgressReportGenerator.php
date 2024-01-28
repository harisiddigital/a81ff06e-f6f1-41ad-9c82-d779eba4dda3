<?php
include_once 'ReportGenerator.php';

class ProgressReportGenerator extends ReportGenerator
{

    private $totalQuestions;
    private $studentResponsesData;

    public function __construct($fullName, $mostRecentResponse, $totalQuestions, $studentResponsesData, $questionsData)
    {
        parent::__construct($fullName, $mostRecentResponse, $questionsData);
        $this->totalQuestions = $totalQuestions;
        $this->studentResponsesData = $studentResponsesData;
    }

    public function generateReport()
    {
        $assessmentCount = 0;
        $oldestRawScore = null;
        $latestRawScore = $this->mostRecentResponse['results']['rawScore'];
        $lastDate = null;

        echo "{$this->fullName} has completed Numeracy assessment ";

        foreach ($this->studentResponsesData as $response) {
            if (!isset($response['completed'])) {
                continue;
            }
            if ($response['student']['id'] == $this->mostRecentResponse['student']['id']) {
                $assessmentCount++;
                $rawScore = $response['results']['rawScore'];

                if ($oldestRawScore === null) {
                    $oldestRawScore = $rawScore;
                    $lastDate = $response['completed'];
                } elseif ($response['completed'] < $lastDate) {
                    $oldestRawScore = $rawScore;
                }
            }
        }

        echo "{$assessmentCount} times in total. Date and raw score given below:\n";

        foreach ($this->studentResponsesData as $response) {
            if (!isset($response['completed'])) {
                continue;
            }
            if ($response['student']['id'] == $this->mostRecentResponse['student']['id']) {
                $responseDate = DateTime::createFromFormat('d/m/Y H:i:s', $response['completed']);
                $rawScore = $response['results']['rawScore'];
                $dateFormatted = $responseDate->format('jS F Y');
                echo "Date: {$dateFormatted}, Raw Score: {$rawScore} out of {$this->totalQuestions}\n";
            }
        }

        $difference = $latestRawScore - $oldestRawScore;
        echo "\n{$this->fullName} got {$difference} more correct in the recent completed assessment than the oldest.\n";
    }
}
