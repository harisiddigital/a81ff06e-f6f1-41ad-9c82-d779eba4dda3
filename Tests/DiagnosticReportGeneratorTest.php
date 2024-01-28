<?php
use PHPUnit\Framework\TestCase;
include_once 'ReportGenerators/DiagnosticReportGenerator.php';

class DiagnosticReportGeneratorTest extends TestCase
{
    public function testGenerateReport()
    {
        // Mock the dependencies or provide necessary data
        $studentDetails = [
            'firstName' => 'John',
            'lastName' => 'Doe',
        ];

        $mostRecentResponse = [
            'student' => [
                'id' => 123,
            ],
            'completed' => '31/12/2021 14:30:00',
            'results' => [
                'rawScore' => 8,
            ],
            'responses' => [
                // Mock response data
                [
                    'questionId' => 'q1',
                    'response' => 'A',
                ],

            ],
        ];

        $questionsData = [
            // Mock questions data
            [
                'id' => 'q1',
                'config' => [
                    'key' => 'A',
                ],
                'strand' => 'Math',
            ],

        ];

        // Create an instance of the DiagnosticReportGenerator class
        $fullName = "{$studentDetails['firstName']} {$studentDetails['lastName']}";
        $assessmentName = 'Diagnostic Report';
        $completedDate = $mostRecentResponse['completed'];
        $reportGenerator = new DiagnosticReportGenerator($fullName, $assessmentName, $completedDate, $mostRecentResponse, $questionsData);

        // Call the method to generate the report
        $report = $reportGenerator->generateReport();

        // Assert that the generated report meets your expectations
        $this->assertStringContainsString('John Doe', $report['fullName']);
        $this->assertStringContainsString('31/12/2021 14:30:00', $report['completedDate']);

    }

}