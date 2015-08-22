<?php

namespace Darsyn\ClassFinder\Tests;

use Darsyn\ClassFinder\BundleClassFinder;
use Darsyn\ClassFinder\Tests\Fixtures\Bundle\TestBundle;
use Darsyn\ClassFinder\Tests\Fixtures\TestKernel;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class BundleClassFinderTest extends ArrayContentsAssertion
{
    /**
     * Get Booted Kernel
     *
     * @access protected
     * @return \Darsyn\ClassFinder\Tests\Fixtures\TestKernel
     */
    protected function getBootedKernel()
    {
        $kernel = new TestKernel;
        $kernel->boot();
        return $kernel;
    }

    /**
     * No Kernel Initialisation
     *
     * @test
     * @access public
     * @return void
     */
    public function noKernelInitialisation()
    {
        $finder = new BundleClassFinder;
        $this->assertAttributeInstanceOf('SplObjectStorage', 'bundles', $finder);
        $this->assertAttributeCount(0, 'bundles', $finder);
    }

    /**
     * Kernel Initialisation
     *
     * @test
     * @access public
     * @return void
     */
    public function kernelInitialisation()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertAttributeInstanceOf('SplObjectStorage', 'bundles', $finder);
        $this->assertAttributeCount(1, 'bundles', $finder);
    }

    /**
     * Set and Add Bundles
     *
     * @test
     * @access public
     * @return void
     */
    public function setAndAddBundles()
    {
        // Test setting the bundles add them to the BundleClassFinder.
        $finder = new BundleClassFinder;
        $finder->setBundles([
            new TestBundle,
        ]);
        $this->assertAttributeCount(1, 'bundles', $finder);
        // Test that adding bundles work.
        $finder->addBundle($bundle = new TestBundle);
        $this->assertAttributeCount(2, 'bundles', $finder);
        // Test that adding the same instance of the bundle does not add duplicates.
        $finder->addBundle($bundle);
        $this->assertAttributeCount(2, 'bundles', $finder);
        // Test that setting bundles resets what's already in BundleClassFinder.
        $finder->setBundles([
            new TestBundle,
        ]);
        $this->assertAttributeCount(1, 'bundles', $finder);
    }

    /**
     * Classes Are Found
     *
     * @test
     * @access public
     * @return void
     */
    public function classesAreFound()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\TestBundle',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Entity\\MyEntity',
        ], $finder->findClasses());
    }

    /**
     * Subdir
     *
     * @test
     * @access public
     * @return void
     */
    public function subdir()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses('Controllers'));
        $this->assertSameArrayContents([], $finder->findClasses('Controller'));
    }

    /**
     * Suffix
     *
     * @test
     * @access public
     * @return void
     */
    public function suffix()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(null, 'Controller'));
        $this->assertSameArrayContents([], $finder->findClasses(null, 'Controllers'));
    }

    /**
     * Parent
     *
     * @test
     * @access public
     * @return void
     */
    public function parent()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(
            null,
            null,
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'
        ));
    }

    /**
     * Subdir, Suffix and Parent
     *
     * @test
     * @access public
     * @return void
     */
    public function subdirSuffixAndParent()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(
            'Controllers',
            'Controller',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'
        ));
    }
}
