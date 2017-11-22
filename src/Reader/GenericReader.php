<?php

namespace GitlabReporter\Reader;

use Symfony\Component\Console\Output\OutputInterface;

abstract class GenericReader implements ReaderInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * GenericReader constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    abstract protected function getHeader(): string;

    abstract protected function processReport(array $report, int $verbosity): string;

    public function read(string $filePath, int $verbosity = OutputInterface::VERBOSITY_NORMAL): string
    {
        $this->output->writeln('<info>Searching for file: '.$filePath.'</info>');

        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \RuntimeException(
                sprintf(
                    'File at path %s is not readable or doesn\'t exist.',
                    $filePath
                )
            );
        }

        $this->output->writeln('<info>File found, reading...</info>');

        $xmlReport = simplexml_load_string(file_get_contents($filePath));
        $jsonReport = json_decode(json_encode($xmlReport), true);

        $markdownReport = "# {$this->getHeader()} \n\n";

        $this->output->writeln('<info>Generating comment...</info>');

        $markdownReport .= $this->processReport($jsonReport, $verbosity);

        $this->output->writeln('<info>Generated!</info>');

        return $markdownReport;
    }
}
