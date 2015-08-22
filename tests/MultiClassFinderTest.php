<?php

namespace Darsyn\ClassFinder\Tests;

use Darsyn\ClassFinder\MultiClassFinder;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class MultiClassFinderTest extends ArrayContentsAssertion
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

    /**
     * Initialisation
     *
     * @test
     * @access public
     * @return void
     */
    public function initialisation()
    {
        $finder = new MultiClassFinder;
        $this->assertAttributeCount(0, 'locations', $finder);
    }

    /**
     * Constructor Locations
     *
     * @test
     * @access public
     * @return void
     */
    public function constructorLocations()
    {
        $finder = new MultiClassFinder($this->getLocations());
        $this->assertAttributeCount(2, 'locations', $finder);
    }

    /**
     * Set Locations
     *
     * @test
     * @access public
     * @return void
     */
    public function setLocations()
    {
        $finder = new MultiClassFinder;
        $this->assertAttributeCount(0, 'locations', $finder);
        $finder->setLocations($this->getLocations());
        $this->assertAttributeCount(2, 'locations', $finder);
    }

    /**
     * Add Locations
     *
     * @test
     * @access public
     * @return void
     */
    public function addLocations()
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

    /**
     * Classes Are Found
     *
     * @test
     * @access public
     * @return void
     */
    public function classesAreFound()
    {
        $finder = new MultiClassFinder($this->getLocations());
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Entity\\MyEntity',
        ], $finder->findClasses());
    }
}
