<?php

namespace Darsyn\ClassFinder;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
interface MultiClassFinderInterface extends ClassFinderInterface
{
    /**
     * Set Locations
     *
     * @access public
     * @param array $locations
     * @return void
     */
    public function setLocations(array $locations);
}
