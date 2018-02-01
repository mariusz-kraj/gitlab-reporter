<?php

namespace GitlabReporter\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class GitlabReporterConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $configuration = new TreeBuilder();
        $root = $configuration->root('gitlab-reporter');

        $root
            ->children()
            ->arrayNode('reporters')
            ->addDefaultsIfNotSet()
            ->children()
            ->enumNode('type')->values(['phpunit', 'phpcs', 'phpmd'])->end()
            ->scalarNode('path')->end()
            ->booleanNode('failIfNotFound')->defaultTrue()->end()
            ->end()
            ->end()
            ->end();

        return $configuration;
    }
}
