<?php

class ReportGenerator
{
    protected $fullName;
    protected $mostRecentResponse;
    protected $questionsData;

    public function __construct($fullName, $mostRecentResponse, $questionsData)
    {
        $this->fullName = $fullName;
        $this->mostRecentResponse = $mostRecentResponse;
        $this->questionsData = $questionsData;
    }

    public function getCorrectOption($questionId, $questionsData)
    {
        foreach ($questionsData as $question) {
            if (isset($question['id']) && $question['id'] === $questionId && isset($question['config']['key'])) {
                return $question['config']['key'];
            }
        }

        return null;
    }

    public function getIncorrectQuestionDetails($questionResponse, $questionsData)
    {
        $questionId = $questionResponse['questionId'];
        $correctOption = $this->getCorrectOption($questionId, $questionsData);

        foreach ($questionsData as $question) {
            if (isset($question['id']) && $question['id'] === $questionId && isset($question['config']['options'])) {
                $options = $question['config']['options'];

                foreach ($options as $option) {
                    if (isset($option['id']) && $option['id'] === $questionResponse['response']) {
                        return [
                            'stem' => $question['stem'],
                            'responseValue' => $option['value'],
                            'correctValue' => isset($options[array_search($correctOption, array_column($options, 'id'))]['value']) ? $options[array_search($correctOption, array_column($options, 'id'))]['value'] : null,
                            'hint' => isset($question['config']['hint']) ? $question['config']['hint'] : null,
                        ];
                    }
                }
            }
        }

        return null;
    }

    // Function to get strand information for a question ID
    public function getStrandById($questionId, $questionsData)
    {
        foreach ($questionsData as $question) {
            if (isset($question['id']) && $question['id'] === $questionId && isset($question['strand'])) {
                return $question['strand'];
            }
        }

        return null;
    }

}