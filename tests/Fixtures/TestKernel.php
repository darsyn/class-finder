<?php

namespace Darsyn\ClassFinder\Tests\Fixtures;


use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class TestKernel extends Kernel
{
    /**
     * Constructor
     * Override the Kernel constructor to force no required parameters for the ClassFinder tests.
     *
     * @access public
     * @param string $environment
     * @param bool $debug
     */
    public function __construct($environment = 'dev', $debug = true)
    {
        $this->environment = $environment;
        $this->debug = (bool) $debug;
    }

    /**
     * Register Bundles
     *
     * @access public
     * @return array
     */
    public function registerBundles()
    {
        return [
            new Bundle\TestBundle,
        ];
    }

    /**
     * Register Container Configuration
     *
     * @access public
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     * @return void
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }

    /**
     * Get Cache Directory
     *
     * @access public
     * @return string
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir();
    }

    /**
     * Get Logs Directory
     *
     * @access public
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir();
    }
}
