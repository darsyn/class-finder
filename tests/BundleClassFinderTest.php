<?php

namespace Darsyn\ClassFinder\Tests;

use Darsyn\ClassFinder\BundleClassFinder;
use Darsyn\ClassFinder\Tests\Fixtures\Bundle\TestBundle;
use Darsyn\ClassFinder\Tests\Fixtures\TestKernel;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class BundleClassFinderTest extends \PHPUnit_Framework_TestCase
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

    public function testNoKernelInitialisation()
    {
        $finder = new BundleClassFinder;
        $this->assertAttributeInstanceOf('SplObjectStorage', 'bundles', $finder);
        $this->assertAttributeCount(0, 'bundles', $finder);
    }

    public function testKernelInitialisation()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertAttributeInstanceOf('SplObjectStorage', 'bundles', $finder);
        $this->assertAttributeCount(1, 'bundles', $finder);
    }

    public function testSetAndAddBundles()
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

    public function testClassesAreFound()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\TestBundle',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Entity\\MyEntity',
        ], $finder->findClasses());
    }

    public function testSubDir()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses('Controllers'));
        $this->assertEquals([], $finder->findClasses('Controller'));
    }

    public function testSuffix()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(null, 'Controller'));
        $this->assertEquals([], $finder->findClasses(null, 'Controllers'));
    }

    public function testParent()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(
            null,
            null,
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'
        ));
    }

    public function testSubDirSuffixAndParent()
    {
        $finder = new BundleClassFinder($this->getBootedKernel());
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(
            'Controllers',
            'Controller',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'
        ));
    }
}
