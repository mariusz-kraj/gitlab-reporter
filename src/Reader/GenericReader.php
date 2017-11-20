<?php

namespace GitlabReporter\Reader;

use Symfony\Component\Console\Output\OutputInterface;

abstract class GenericReader implements ReaderInterface
{
    abstract protected function getHeader(): string;

    abstract protected function processReport(array $report, int $verbosity): string;

    public function read(string $filePath, int $verbosity = OutputInterface::VERBOSITY_NORMAL): string
    {
        $xmlReport = simplexml_load_string(file_get_contents($filePath));
        $jsonReport = json_decode(json_encode($xmlReport), true);

//        file_put_contents('coverage.json', json_encode($jsonReport));

        $markdownReport = "# {$this->getHeader()} \n\n";

        $markdownReport .= $this->processReport($jsonReport, $verbosity);

        return $markdownReport;
    }
}
