<?php

namespace GitlabReporter\Reader;

class PhpMessDetectorReader extends GenericReader
{
    protected function getHeader(): string
    {
        return 'Php Mess Detector Report';
    }

    /**
     * This method reads report from Php Mess Detector and return list of files with issues.
     *
     * @param array $report
     * @param int $verbosity
     * @return string
     */
    public function processReport(array $report, int $verbosity): string
    {
        $checkstyleReport = "## Violations\n\n";

        foreach ($report['file'] as $fileMess) {
            $issueFilePath = $fileMess['@attributes']['name'];

            $relativeIssueFilePath = str_replace(getcwd(), '', $issueFilePath);

            $issueFileName = explode('/', $issueFilePath);
            $issueFileName = end($issueFileName);


            $fileReport = sprintf(
                "In [*%s*](%s) file, we founded issues:\n",
                $issueFileName,
                $relativeIssueFilePath
            );

            if (!is_array($fileMess['violation'])) {
                $fileMess['violation'] = [
                    $fileMess['violation'],
                ];
            }

            foreach ($fileMess['violation'] as $violation) {
                $fileReport .= sprintf(
                    "*  %s\n",
                    $violation
                );
            }

            $fileReport .= "\n\n";

            $checkstyleReport .= $fileReport;
        }

        if (false === isset($report['error'])) {
            return $checkstyleReport;
        }

        $checkstyleReport .= "## Errors\n\n";
        foreach ($report['error'] as $fileMess) {
            $issueFilePath = $fileMess['@attributes']['filename'];


            $cwd = '/builds/codibly-coders/workbuzz-backend/';
            $relativeIssueFilePath = str_replace($cwd, '', $issueFilePath);

            $issueFileName = explode('/', $issueFilePath);
            $issueFileName = end($issueFileName);


            $fileReport = sprintf(
                "In [*%s*](%s) file, we founded issues:\n",
                $issueFileName,
                $relativeIssueFilePath
            );

            $fileReport .= sprintf(
                "*  %s\n",
                $fileMess['@attributes']['msg']
            );

            $fileReport .= "\n\n";

            $checkstyleReport .= $fileReport;
        }

        return $checkstyleReport;
    }
}
