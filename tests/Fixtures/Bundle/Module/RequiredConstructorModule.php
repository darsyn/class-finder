<?php

namespace Darsyn\ClassFinder\Tests\Fixtures\Bundle\Module;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class RequiredConstructorModule
{
    /**
     * Constructor
     * This class should never be loaded by the ClassFinder because its constructor has one or more required parameters.
     *
     * @access public
     * @param mixed $requiredParameter
     * @param mixed $optionalParameter
     */
    public function __construct($requiredParameter, $optionalParameter = null)
    {
    }
}
