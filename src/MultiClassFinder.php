<?php

namespace Darsyn\ClassFinder;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class MultiClassFinder extends ClassFinder
{
    /**
     * @access protected
     * @var array
     */
    protected $locations = array();

    /**
     * Constructor
     *
     * @access public
     * @param array $locations
     */
    public function __construct(array $locations = array())
    {
        $this->locations = $locations;
    }

    /**
     * Set Locations
     *
     * @access public
     * @param array $locations
     * @return self
     */
    public function setLocations(array $locations)
    {
        $this->locations = array();
        foreach ($locations as $namespace => $location) {
            $this->addLocation($namespace, $location);
        }
        return $this;
    }

    /**
     * Add Location
     *
     * @access public
     * @param string $namespace
     * @param string $location
     * @return self
     */
    public function addLocation($namespace, $location)
    {
        $this->locations[$namespace] = $location;
        return $this;
    }

    /**
     * Find Classes
     *
     * @access public
     * @param string $subDir
     * @param string $suffix
     * @param string $parent
     * @param boolean $reflection
     * @return array
     */
    public function findClasses($subDir = null, $suffix = null, $parent = null, $reflection = false)
    {
        $classes = [];
        foreach ($this->locations as $namespace => $location) {
            $this->setRootDirectory($location);
            $this->setRootNamespace($namespace);
            $classes = array_merge($classes, parent::findClasses($subDir, $suffix, $parent, $reflection));
        }
        return $classes;
    }
}
