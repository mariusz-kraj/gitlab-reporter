<?php

namespace GitlabReporter\Reader;

use Symfony\Component\Console\Output\OutputInterface;

interface ReaderInterface
{
    /**
     * Method take file path as a argument and should return valid Markdown string.
     * This string will be placed in the merge request comment.
     *
     * @param string $filePath
     * @param int $verbosity Value from OutputInterface
     * @return string
     */
    public function read(string $filePath, int $verbosity = OutputInterface::VERBOSITY_NORMAL): string;
}
