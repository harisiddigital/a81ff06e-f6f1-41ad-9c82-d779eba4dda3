<?php
include 'Helpers/helpers.php';
include 'ReportGenerators/DiagnosticReportGenerator.php';
include 'ReportGenerators/ProgressReportGenerator.php';
include 'ReportGenerators/FeedbackReportGenerator.php';

$assessmentData = json_decode(file_get_contents('data/assessments.json'), true);
$studentData = json_decode(file_get_contents('data/students.json'), true);
$studentResponsesData = json_decode(file_get_contents('data/student-responses.json'), true);
$questionsData = json_decode(file_get_contents('data/questions.json'), true);

$studentId = readline("Enter student ID: ");
$reportType = readline("Enter report type (1: Diagnostic, 2: Progress, 3: Feedback): ");

$studentDetails = getStudentDetailsById($studentData, $studentId);

if ($studentDetails) {
    $mostRecentResponse = getMostRecentResponse($studentResponsesData, $studentId);

    if ($mostRecentResponse) {
        $fullName = "{$studentDetails['firstName']} {$studentDetails['lastName']}";
        $assessmentName = $assessmentData[0]['name'];
        $completedDate = $mostRecentResponse['completed'];

        switch ($reportType) {
            case 1:
                $generator = new DiagnosticReportGenerator($fullName, $assessmentName, $completedDate, $mostRecentResponse, $questionsData);
                $generator->generateReport();
                break;
            case 2:
                $generator = new ProgressReportGenerator($fullName, $mostRecentResponse, count($questionsData), $studentResponsesData, $questionsData);
                $generator->generateReport();
                break;
            case 3:
                $generator = new FeedbackReportGenerator($fullName, $assessmentName, $completedDate, $mostRecentResponse, $questionsData);
                $generator->generateReport();
                break;
            default:
                echo "Invalid report type selected.\n";
        }
    } else {
        echo "No response found for the student ID.\n";
    }
} else {
    echo "Student information is incomplete.\n";
}

