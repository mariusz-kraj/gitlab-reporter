<?php

namespace GitlabReporter\Command;

use GitlabReporter\Client\GuzzleClient;
use GitlabReporter\Reader\PhpCodeSnifferReader;
use GitlabReporter\Reader\PhpMessDetectorReader;
use GitlabReporter\Reader\PhpUnitReader;
use GitlabReporter\Reader\ReaderInterface;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ReportCommand extends Command
{
    private $reporters = [
        'phpunit' => PhpUnitReader::class,
        'phpmd' => PhpMessDetectorReader::class,
        'phpcs' => PhpCodeSnifferReader::class,
    ];

    protected function configure()
    {
        $this
            ->setName('publish')
            ->setDescription('Publish reports from the jUnit compatible report')
            ->setHelp('This command allows you to create a user...')
            ->addArgument(
                'config-file',
                InputArgument::OPTIONAL,
                'Path to the result file',
                'gitlab-ci-reporter.yml'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (false === getenv('CI')) {
            $output->writeln(sprintf("<error>This command can be run only in CI environment</error>"));
            return 2;
        }

        if (false === $accessToken = getenv('ACCESS_TOKEN')) {
            $output->writeln(sprintf("<error>This command require ACCESS_TOKEN environmental variable</error>"));
            return 2;
        }


        $configFile = $input->getArgument('config-file');

        if (false === file_exists($configFile)) {
            $output->writeln(sprintf("<error>Unable to access config file at %s</error>", $configFile));

            return 2;
        }

        try {
            $output->writeln(
                [
                    'Gitlab Reporter',
                    '===============',
                ]
            );

            $output->writeln(sprintf('<info>Config file: %s</info>', $configFile));

            // Let's assume that config is great, validation in next stage
            $config = Yaml::parse(file_get_contents($configFile));

            $gitlab = new GuzzleClient($accessToken);

            $mergeRequest = $gitlab->getMergeRequestFromBranch(
                getenv('CI_PROJECT_NAME'),
                getenv('CI_COMMIT_REF_NAME')
            );

            foreach ($config['reporters'] as $reporterAlias => $reporterConfig) {
                $output->writeln(sprintf('<info>Processing reporter: %s</info>', $reporterAlias));

                /** @var ReaderInterface $reporter */
                $reporter = new $this->reporters[$reporterAlias]();

                $note = $reporter->read($reporterConfig['path']);

                $gitlab->postCommentToMergeRequest(
                    getenv('CI_PROJECT_NAME'),
                    $mergeRequest['iid'],
                    $note
                );

                $output->writeln(sprintf('<info>Comment published</info>'));
            }

            $output->writeln(sprintf('<info>Finished</info>'));

            return 0;
        } catch (ParseException $e) {
            $output->writeln(sprintf("<error>Unable to parse the YAML string: %s</error>", $e->getMessage()));

            return 2;
        } catch(ClientException $e) {
            $output->writeln(sprintf("<error>Unable to finish request: %s</error>", $e->getMessage()));

            return 2;
        }
    }

}
