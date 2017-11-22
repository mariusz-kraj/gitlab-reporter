<?php

namespace GitlabReporter\Reader;

use GitlabReporter\Reader\Markdown\TextTable;
use Symfony\Component\Console\Output\OutputInterface;

class PhpCodeSnifferReader extends GenericReader
{
    protected function getHeader(): string
    {
        return 'Php Code Sniffer Report';
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
        $checkstyleReport = '';

        foreach ($report['file'] as $checkstyleIssue) {
            $issueFilePath = $checkstyleIssue['@attributes']['name'];

            $relativeIssueFilePath = str_replace(getcwd(), '', $issueFilePath);

            $issueFileName = explode('/', $issueFilePath);
            $issueFileName = end($issueFileName);


            $fileReport = sprintf(
                "In [*%s*](%s) file, we founded issues:\n",
                $issueFileName,
                $relativeIssueFilePath
            );


            if (isset($checkstyleIssue['error'])) {
                if (isset($checkstyleIssue['error']['@attributes'])) {
                    $checkstyleIssue['error'] = [
                        $checkstyleIssue['error'],
                    ];
                }

                foreach ($checkstyleIssue['error'] as $error) {
                    $fileReport .= sprintf(
                        "*  %s [Line %s](%s#L%s) (%s)\n",
                        $error['@attributes']['message'],
                        $error['@attributes']['line'],
                        $relativeIssueFilePath,
                        $error['@attributes']['line'],
                        $error['@attributes']['source']
                    );
                }
            }

            $fileReport .= "\n\n";

            $checkstyleReport .= $fileReport;
        }

        return $checkstyleReport;
    }
}
