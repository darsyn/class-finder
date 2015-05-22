<?php

namespace Darsyn\ClassFinder\Reflection;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class ReflectionClass extends \ReflectionClass
{
    /**
     * @access protected
     * @var string
     */
    protected $relative;

    /**
     * Constructor
     *
     * @access public
     * @param mixed $class
     * @param string $relative
     */
    public function __construct($class, $relative)
    {
        parent::__construct($class);
        $this->relative = trim(preg_replace('#/{2,}#', '/', strtr($relative, '\\', '/')), '/');
    }

    /**
     * Get Relative Directory
     *
     * @access public
     * @return string
     */
    public function getRelativeDirectory()
    {
        return $this->relative;
    }

    /**
     * Get Relative Namespace
     *
     * @access public
     * @return string
     */
    public function getRelativeNamespace()
    {
        return strtr($this->getRelative(), '/', '\\');
    }
}