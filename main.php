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
function generateDiagnosticReportForRecentResponse($assessmentData, $studentData, $studentResponsesData, $questionsData, $studentId)
{
    // Find the most recent student response for the specified student ID
    $mostRecentResponse = null;
    foreach ($studentResponsesData as $response) {
        if ($response['student']['id'] == $studentId && isset($response['completed'])) {
            $responseDate = DateTime::createFromFormat('d/m/Y H:i:s', $response['completed']);
            $currentMostRecentDate = ($mostRecentResponse !== null) ? DateTime::createFromFormat('d/m/Y H:i:s', $mostRecentResponse['completed']) : null;

            if ($currentMostRecentDate === null || $responseDate > $currentMostRecentDate) {
                $mostRecentResponse = $response;
            }
        }
    }

    // Check if a response exists for the specified student ID
    if ($mostRecentResponse === null) {
        echo "No recent responses found for the provided student ID.\n";
        return;
    }

    // Generate diagnostic report for the most recent response (in the example response its generating for the most recent response do I'm assuming that's what you wanted)
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
    $questionsTotalByStrand = [];
    // Check each response against the correct answer
    foreach ($mostRecentResponse['responses'] as $questionResponse) {
        $questionId = $questionResponse['questionId'];
        $correctOption = getCorrectOption($questionId, $questionsData);

        // Find strand for the question
        $strand = '';
        foreach ($questionsData as $question) {
            if ($question['id'] == $questionId) {
                $strand = $question['strand'];
                break; // Exit the loop once the strand is found
            }
        }

        // Increment the total number of questions in the strand
        if (!isset($questionsTotalByStrand[$strand])) {
            $questionsTotalByStrand[$strand] = 1;
        } else {
            $questionsTotalByStrand[$strand]++;
        }

        // Check if the response is correct
        if ($questionResponse['response'] == $correctOption) {
            $questionsCorrect++;

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
        $questionsTotalInStrand = $questionsTotalByStrand[$strand];
        echo "{$strand}: {$count} out of {$questionsTotalInStrand}\n";
    }

    echo "\n";
}

// Function to generate progress report for all completed assessments of a student
function generateProgressReport($assessmentData, $studentData, $studentResponsesData, $questionsData, $studentId)
{
    // Find all completed assessments for the specified student ID
    $completedAssessments = [];
    foreach ($studentResponsesData as $response) {
        if ($response['student']['id'] == $studentId && isset($response['completed'])) {
            $assessmentId = $response['assessmentId'];
            $assessmentName = '';

            // Find assessment name
            foreach ($assessmentData as $assessment) {
                if ($assessment['id'] == $assessmentId) {
                    $assessmentName = $assessment['name'];
                    break;
                }
            }

            // Parse completion date
            $completionDate = DateTime::createFromFormat('d/m/Y H:i:s', $response['completed']);

            // Get raw score
            $rawScore = $response['results']['rawScore'];

            // Save assessment details
            $completedAssessments[] = [
                'date' => $completionDate->format('jS F Y'),
                'rawScore' => "{$rawScore} out of " . count($questionsData),
            ];
        }
    }

    // Sort assessments by completion date in descending order
    usort($completedAssessments, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    // Check if assessments are available
    if (empty($completedAssessments)) {
        echo "No completed assessments found for the provided student ID.\n";
        return;
    }

    // Display progress report
    $studentDetails = '';
    foreach ($studentData as $student) {
        if ($student['id'] == $studentId) {
            $studentDetails = $student['firstName'] . ' ' . $student['lastName'];
            break;
        }
    }

    echo "{$studentDetails} has completed {$assessmentData[0]['name']} assessment " . count($completedAssessments) . " times in total. Date and raw score given below:\n";

    // Display assessment details
    foreach ($completedAssessments as $assessment) {
        echo "Date: {$assessment['date']}, Raw Score: {$assessment['rawScore']}\n";
    }

    // Calculate improvement in the most recent assessment compared to the oldest one
    $oldestScore = $completedAssessments[count($completedAssessments) - 1]['rawScore'];
    $mostRecentScore = $completedAssessments[0]['rawScore'];

    // Extract scores from strings
    preg_match_all('/\d+/', $oldestScore, $oldestScores);
    preg_match_all('/\d+/', $mostRecentScore, $mostRecentScores);

    // Calculate improvement
    $improvement = $mostRecentScores[0][0] - $oldestScores[0][0];

    echo "\n{$studentDetails} got {$improvement} more correct in the recent completed assessment than the oldest.\n";
}



// Get user input for student ID
echo "Enter the student ID: ";
$studentIdInput = trim(fgets(STDIN));

// Get user input for report type
echo "Enter the report type (1 for Diagnostic Report, 2 for Progress Report): ";
$reportType = trim(fgets(STDIN));

// Generate and display the report based on user input
if ($reportType == 1) {
    generateDiagnosticReportForRecentResponse($assessmentData, $studentData, $studentResponsesData, $questionsData, $studentIdInput);
} elseif ($reportType == 2) {
    generateProgressReport($assessmentData, $studentData, $studentResponsesData, $questionsData, $studentIdInput);
} else {
    echo "Invalid report type. Please enter 1 for Diagnostic Report or 2 for Progress Report.\n";
}
