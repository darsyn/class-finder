<?php

namespace Darsyn\ClassFinder\Tests;

use Darsyn\ClassFinder\MultiClassFinder;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class MultiClassFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Get Locations
     *
     * @access protected
     * @return array
     */
    protected function getLocations()
    {
        return [
            __NAMESPACE__ . '\\Fixtures\\Bundle\\Controllers' => __DIR__ . '/Fixtures/Bundle/Controllers',
            __NAMESPACE__ . '\\Fixtures\\Bundle\\Entity' => __DIR__ . '/Fixtures/Bundle/Entity',
        ];
    }

    public function testInitialisation()
    {
        $finder = new MultiClassFinder;
        $this->assertAttributeCount(0, 'locations', $finder);
    }

    public function testConstructorLocations()
    {
        $finder = new MultiClassFinder($this->getLocations());
        $this->assertAttributeCount(2, 'locations', $finder);
    }

    public function testSetLocations()
    {
        $finder = new MultiClassFinder;
        $this->assertAttributeCount(0, 'locations', $finder);
        $finder->setLocations($this->getLocations());
        $this->assertAttributeCount(2, 'locations', $finder);
    }

    public function testAddLocations()
    {
        $finder = new MultiClassFinder;
        $locations = $this->getLocations();
        $i = 0;
        foreach ($locations as $namespace => $location) {
            $this->assertAttributeCount($i, 'locations', $finder);
            $finder->addLocation($namespace, $location);
            $i++;
        }
        $this->assertAttributeCount(count($locations), 'locations', $finder);
    }

    public function testClassesAreFound()
    {
        $finder = new MultiClassFinder($this->getLocations());
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Entity\\MyEntity',
        ], $finder->findClasses());
    }
}
