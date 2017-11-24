<?php

namespace GitlabReporter\Reader;

use GitlabReporter\Reader\Markdown\TextTable;

class PhpUnitReader extends GenericReader
{
    const EMOJI_OK = ':white_check_mark:';
    const EMOJI_FAILURE = ':x:';

    const STATUS_FAILURE = 'Failure';
    const STATUS_ERROR = 'Error';

    protected function getHeader(): string
    {
        return 'PHPUnit Report';
    }

    /**
     * This method reads report from PhpUnit and return formatted summary of the tests.
     *
     * @param array $report
     * @param int $verbosity
     * @return string
     */
    public function processReport(array $report, int $verbosity): string
    {
        $markdownReport = $this->getTestSuitesOverview($report);

        $markdownReport .= "\n\n";
        $markdownReport .= $this->getTestSuitesDetails($report);

        return $markdownReport;
    }

    private function getTestSuitesOverview(array $jsonReport): string
    {
        $reportTableHeader = ['Test Suite', 'Tests', 'Assertions', 'Errors', 'Failures', 'Skipped', 'Duration', 'Status'];
        $reportTableRows = [];

        foreach ($jsonReport['testsuite'] as $testSuite) {

            $errors = (int) $testSuite['@attributes']['errors'];
            $failures = (int) $testSuite['@attributes']['failures'];

            $reportTableRows[] = [
                $testSuite['@attributes']['name'],
                $testSuite['@attributes']['tests'],
                $testSuite['@attributes']['assertions'],
                $errors,
                $failures,
                isset($testSuite['@attributes']['skipped']) ? $testSuite['@attributes']['skipped'] : 'N/A',
                $testSuite['@attributes']['time'],
                $this->getStatus($errors, $failures)
            ];
        }

        $markdownTable = new TextTable($reportTableHeader, $reportTableRows);

        $header = "# Overview\n\n";
        return $header . $markdownTable->render();

    }

    private function getTestSuitesDetails(array $jsonReport): string
    {
        $reportTableHeader = ['Test Suite', 'Name', 'Class', 'File', 'Status'];
        $reportTableRows = [];

        foreach ($jsonReport['testsuite'] as $testSuite) {
            $testSuiteName = $testSuite['@attributes']['name'];

            foreach ($testSuite['testcase'] as $testCase) {
                if (!isset($testCase['failure']) && !isset($testCase['error'])) {
                    continue;
                }

                $reportTableRows[] = [
                    $testSuiteName,
                    $testCase['@attributes']['name'],
                    $testCase['@attributes']['class'],
                    $testCase['@attributes']['file'],
                    isset($testCase['failure']) ? self::STATUS_FAILURE : self::STATUS_ERROR
                ];
            }
        }

        $markdownTable = new TextTable($reportTableHeader, $reportTableRows);

        $header = "# Failures or Errors \n\n";
        return $header.$markdownTable->render();
    }

    private function getStatus(int $errors, int $failures)
    {
        if ($errors > 0 || $failures > 0) {
            return self::EMOJI_FAILURE;
        }

        return self::EMOJI_OK;
    }
}
