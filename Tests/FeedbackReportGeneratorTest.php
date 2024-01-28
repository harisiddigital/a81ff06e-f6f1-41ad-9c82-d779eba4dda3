<?php
use PHPUnit\Framework\TestCase;
include_once 'ReportGenerators/FeedbackReportGenerator.php';

class FeedbackReportGeneratorTest extends TestCase
{
    public function testGenerateFeedbackReport()
    {
        // Mock data for the most recent response
        $mostRecentResponse = [
            'results' => [
                'rawScore' => 15,
            ],
            'responses' => [
                [
                    'questionId' => 'question1',
                    'response' => 'A',
                ],

            ],
        ];

        // Mock data for questions
        $questionsData = [
            [
                'id' => 'question1',
                'stem' => 'What is the median?',
                'config' => [
                    'options' => [
                        ['id' => 'A', 'value' => '5'],
                        ['id' => 'B', 'value' => '7'],

                    ],
                    'hint' => 'Arrange numbers in ascending order and find the middle term.',
                ],
            ],

        ];

        $fullName = 'Tony Stark';
        $assessmentName = 'Numeracy Assessment';
        $completedDate = '16th December 2021';

        $feedbackReportGenerator = new FeedbackReportGenerator($fullName, $assessmentName, $completedDate, $mostRecentResponse, $questionsData);
        $feedbackReport = $feedbackReportGenerator->generateReport();

        // Assertions
        $this->assertStringContainsString($fullName, $feedbackReport['fullName']);
        $this->assertStringContainsString($assessmentName, $feedbackReport['assessmentName']);
        $this->assertStringContainsString($completedDate, $feedbackReport['completedDate']);
    }
}