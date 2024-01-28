<?php
include_once 'ReportGenerator.php';

class DiagnosticReportGenerator extends ReportGenerator
{
    private $assessmentName;
    private $completedDate;

    public function __construct($fullName, $assessmentName, $completedDate, $mostRecentResponse, $questionsData)
    {
        parent::__construct($fullName, $mostRecentResponse, $questionsData);
        $this->assessmentName = $assessmentName;
        $this->completedDate = $completedDate;
    }

    public function generateReport()
    {
        $rawScore = $this->mostRecentResponse['results']['rawScore'];
        $totalQuestions = count($this->mostRecentResponse['responses']);

        echo "{$this->fullName} recently completed {$this->assessmentName} assessment on {$this->completedDate}\n";
        echo "He got {$rawScore} questions right out of {$totalQuestions}. \n";

        $strandCounts = array();
        foreach ($this->mostRecentResponse['responses'] as $questionResponse) {
            $questionId = $questionResponse['questionId'];
            $correctOption = $this->getCorrectOption($questionId, $this->questionsData);
            $strand = $this->getStrandById($questionId, $this->questionsData);

            if ($questionResponse['response'] == $correctOption) {
                $strandCounts[$strand]['correct'] = isset($strandCounts[$strand]['correct']) ? $strandCounts[$strand]['correct'] + 1 : 1;
            }
            $strandCounts[$strand]['total'] = isset($strandCounts[$strand]['total']) ? $strandCounts[$strand]['total'] + 1 : 1;
        }

        echo "\nDetails by strand given below:\n";
        foreach ($strandCounts as $strand => $countDetails) {
            echo "{$strand}: {$countDetails['correct']} out of {$countDetails['total']}\n";
        }
        
        // Return details as required in test cases
        return [
            'fullName' => $this->fullName,
            'assessmentName' => $this->assessmentName,
            'completedDate' => $this->completedDate,
            'rawScore' => $rawScore,
            'totalQuestions' => $totalQuestions,
            'strandCounts' => $strandCounts
        ];
    }
}
