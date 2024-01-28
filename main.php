<?php

// Read JSON files
$assessmentData = json_decode(file_get_contents('data/assessments.json'), true);
$studentData = json_decode(file_get_contents('data/students.json'), true);
$studentResponsesData = json_decode(file_get_contents('data/student-responses.json'), true);
$questionsData = json_decode(file_get_contents('data/questions.json'), true);

// Function to find the correct option for a given question
function getCorrectOption($questionId, $questionsData)
{
    foreach ($questionsData as $question) {
        if ($question['id'] == $questionId) {
            return $question['config']['key'];
        }
    }
    return null;
}

// Function to generate diagnostic report for the most recent student response
function generateDiagnosticReportForRecentResponse($assessmentData, $studentData, $studentResponsesData, $questionsData)
{
    // Find the most recent student response
    $mostRecentResponse = null;
    foreach ($studentResponsesData as $response) {
        if (isset($response['completed'])) {
            $responseDate = DateTime::createFromFormat('d/m/Y H:i:s', $response['completed']);
            $currentMostRecentDate = ($mostRecentResponse !== null) ? DateTime::createFromFormat('d/m/Y H:i:s', $mostRecentResponse['completed']) : null;

            if ($currentMostRecentDate === null || $responseDate > $currentMostRecentDate) {
                $mostRecentResponse = $response;
            }
        }
    }

    // Generate diagnostic report for the most recent response (in the example response its generating for the most recent response do I'm assuming that's what you wanted)
    if ($mostRecentResponse !== null) {
        $assessmentId = $mostRecentResponse['assessmentId'];
        $assessmentName = '';
        $questionsCorrect = 0;
        $questionsTotal = count($mostRecentResponse['responses']);
        $strandCorrectCount = [];

        // Find assessment name
        foreach ($assessmentData as $assessment) {
            if ($assessment['id'] == $assessmentId) {
                $assessmentName = $assessment['name'];
                break;
            }
        }

        // Find student details
        $studentDetails = '';
        foreach ($studentData as $student) {
            if ($student['id'] == $mostRecentResponse['student']['id']) {
                $studentDetails = $student['firstName'] . ' ' . $student['lastName'];
                break;
            }
        }

        // Check each response against the correct answer
        foreach ($mostRecentResponse['responses'] as $questionResponse) {
            $questionId = $questionResponse['questionId'];
            $correctOption = getCorrectOption($questionId, $questionsData);

            // Check if the response is correct
            if ($questionResponse['response'] == $correctOption) {
                $questionsCorrect++;
                $strand = '';
                // Find strand for the question
                foreach ($questionsData as $question) {
                    if ($question['id'] == $questionId) {
                        $strand = $question['strand'];
                        break;
                    }
                }

                // Count correct responses by strand
                if (!isset($strandCorrectCount[$strand])) {
                    $strandCorrectCount[$strand] = 1;
                } else {
                    $strandCorrectCount[$strand]++;
                }
            }
        }

        // Display the diagnostic report for the most recent response
        echo "{$studentDetails} recently completed {$assessmentName} assessment on {$mostRecentResponse['completed']}\n";
        echo "He got {$questionsCorrect} questions right out of {$questionsTotal}. Details by strand given below:\n";

        // Display correct count for each strand
        foreach ($strandCorrectCount as $strand => $count) {
            echo "{$strand}: {$count} out of {$count}\n";
        }

        echo "\n";
    } else {
        echo "No completed responses found.\n";
    }
}

// Generate and display diagnostic report for the most recent response
generateDiagnosticReportForRecentResponse($assessmentData, $studentData, $studentResponsesData, $questionsData);

?>
