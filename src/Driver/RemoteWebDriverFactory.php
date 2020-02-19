<?php

namespace Axelerant\ApplitoolsEyes\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\Selenium2Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

class RemoteWebDriverFactory extends Selenium2Factory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'remote_web_driver';
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        // Merge capabilities
        $extraCapabilities = $config['capabilities']['extra_capabilities'];
        unset($config['capabilities']['extra_capabilities']);
        $capabilities = array_replace($this->guessCapabilities(), $extraCapabilities, $config['capabilities']);

        // Build driver definition
        return new Definition(RemoteWebDriver::class, [
          $config['wd_host'],
          $capabilities,
          $config['connection_timeout_in_ms'],
          $config['request_timeout_in_ms'],
          $config['http_proxy'],
          $config['http_proxy_port'],
          $config['required_capabilities'],
        ]);
    }

    /**
     * Guess capabilities from environment
     *
     * @return array
     */
    protected function guessCapabilities()
    {
        if (getenv('TRAVIS_JOB_NUMBER')) {
            return [
              'tunnel-identifier' => getenv('TRAVIS_JOB_NUMBER'),
              'build' => getenv('TRAVIS_BUILD_NUMBER'),
              'tags' => ['Travis-CI', 'PHP ' . phpversion()],
            ];
        }

        if (getenv('JENKINS_HOME')) {
            return [
              'tunnel-identifier' => getenv('JOB_NAME'),
              'build' => getenv('BUILD_NUMBER'),
              'tags' => ['Jenkins', 'PHP ' . phpversion(), getenv('BUILD_TAG')],
            ];
        }

        return [
          'tags' => [php_uname('n'), 'PHP ' . phpversion()],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder->children()
            ->scalarNode('browser')->defaultValue('%mink.browser_name%')->end()
            ->scalarNode('connection_timeout_in_ms')->defaultValue(null)->end()
            ->scalarNode('request_timeout_in_ms')->defaultValue(null)->end()
            ->scalarNode('http_proxy')->defaultValue(null)->end()
            ->scalarNode('http_proxy_port')->defaultValue(null)->end()
            ->scalarNode('required_capabilities')->defaultValue(null)->end()
        ->end();
        parent::configure($builder);
    }


    /**
     * {@inheritDoc}
     */
    protected function getCapabilitiesNode()
    {
        $node = parent::getCapabilitiesNode();
        // Override default browser to chrome
        $node
            ->children()
            ->scalarNode('browser')->defaultValue('chrome')->end()
            ->end();
        return $node;
    }

}
