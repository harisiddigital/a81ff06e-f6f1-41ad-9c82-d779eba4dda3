# Code Challenge

This project is a code challenge that involves generating diagnostic, progress, and feedback reports based on student assessment data. Provided to me on behalf of ACER. 
I have made some assumptions. To create Diagnostic report I have chosen the most recent response as that is what was shown in the example response in the challange. 
For automated testing I have configured github actions to run the 2 tests automatically. There reports can be seen in the "Actions" tab of this repository.

## Getting Started

### Prerequisites

- PHP (>= 8.2)
- Composer

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/harisiddigital/a81ff06e-f6f1-41ad-9c82-d779eba4dda3.git
2. Change into the project directory:
   
   ```bash
   cd a81ff06e-f6f1-41ad-9c82-d779eba4dda3


3. Install dependencies:
   
   ```bash
   composer install

4. Run the main script:
   ```bash
   php main.php

Follow the prompts to enter the student ID and select the type of report (1 for Diagnostic, 2 for Progress, 3 for Feedback).

## Running Tests:

To run PHPUnit tests, use the following command:

1. For Diagnostic report test:
   
   ```bash
   vendor/bin/phpunit Tests/DiagnosticReportGeneratorTest.php


2. For Feedback report test:
   
   ```bash
   vendor/bin/phpunit Tests/FeedbackReportGeneratorTest.php

## Video
See the following link to see how to set up and how to use this CLI tool:
https://www.loom.com/share/7bb90ca4fbe44a2a9880150f80475a8f

## Project Structure

- `main.php`: The main script to interact with the user and generate reports based on user input.
- `helpers.php`: Helper functions used in the report generation process.
- `ReportGenerators/`: Directory containing classes for each type of report.
    - `DiagnosticReportGenerator.php`: Class for generating Diagnostic Reports.
    - `ProgressReportGenerator.php`: Class for generating Progress Reports.
    - `FeedbackReportGenerator.php`: Class for generating Feedback Reports.
- `data/`: Directory containing JSON data files for assessments, students, student responses, and questions.
- `tests/`: Directory containing PHPUnit test files for each report generator.
