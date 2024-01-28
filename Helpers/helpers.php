<?php

function getMostRecentResponse($studentResponsesData, $studentId)
{
    $mostRecentResponse = null;

    foreach ($studentResponsesData as $response) {
        if ($response['student']['id'] == $studentId && array_key_exists('completed', $response)) {
            $responseDate = DateTime::createFromFormat('d/m/Y H:i:s', $response['completed']);
            $currentMostRecentDate = ($mostRecentResponse !== null) ? DateTime::createFromFormat('d/m/Y H:i:s', $mostRecentResponse['completed']) : null;

            if ($currentMostRecentDate === null || $responseDate > $currentMostRecentDate) {
                $mostRecentResponse = $response;
            }
        }
    }

    return $mostRecentResponse;
}

function getStudentDetailsById($studentData, $studentId)
{
    foreach ($studentData as $student) {
        if ($student['id'] == $studentId) {
            return $student;
        }
    }

    return null;
}

