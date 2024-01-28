<?php
include_once 'ReportGenerator.php';


class FeedbackReportGenerator extends ReportGenerator
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
        echo "He got {$rawScore} questions right out of {$totalQuestions}. Feedback for wrong answers given below:\n";

        foreach ($this->mostRecentResponse['responses'] as $questionResponse) {
            $questionId = $questionResponse['questionId'];
            $correctOption = $this->getCorrectOption($questionId, $this->questionsData);

            if ($questionResponse['response'] != $correctOption) {
                $incorrectQuestionDetails = $this->getIncorrectQuestionDetails($questionResponse, $this->questionsData);

                if ($incorrectQuestionDetails) {
                    echo "\nQuestion: {$incorrectQuestionDetails['stem']}\n";
                    echo "Your answer: {$questionResponse['response']} with value {$incorrectQuestionDetails['responseValue']}\n";
                    echo "Right answer: {$correctOption} with value {$incorrectQuestionDetails['correctValue']}\n";
                    echo "Hint: {$incorrectQuestionDetails['hint']}\n";
                }
            }
        }
        // Return details as required in test cases
        return [
            'fullName' => $this->fullName,
            'assessmentName' => $this->assessmentName,
            'completedDate' => $this->completedDate,
            'rawScore' => $rawScore,
            'totalQuestions' => $totalQuestions,
        ];
    }
}
