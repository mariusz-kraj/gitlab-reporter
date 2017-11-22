<?php

namespace GitlabReporter\Reader;

use Symfony\Component\Console\Output\OutputInterface;

abstract class GenericReader implements ReaderInterface
{
    abstract protected function getHeader(): string;

    abstract protected function processReport(array $report, int $verbosity): string;

    public function read(string $filePath, int $verbosity = OutputInterface::VERBOSITY_NORMAL): string
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \RuntimeException(
                sprintf(
                    'File at path %s is not readable or doesn\'t exist.',
                    $filePath
                )
            );
        }

        $xmlReport = simplexml_load_string(file_get_contents($filePath));
        $jsonReport = json_decode(json_encode($xmlReport), true);

        $markdownReport = "# {$this->getHeader()} \n\n";

        $markdownReport .= $this->processReport($jsonReport, $verbosity);

        return $markdownReport;
    }
}
