<?php

namespace Darsyn\ClassFinder;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
interface ClassFinderInterface
{
    /**
     * Set Root Directory
     *
     * @param $directory
     * @return void
     */
    public function setRootDirectory($directory);

    /**
     * Set Root Namespace
     *
     * @param $namespace
     * @return void
     */
    public function setRootNamespace($namespace);

    /**
     * Set the file extension to search for (without leading full-stop).
     *
     * @param string $ext
     * @return void
     */
    public function setExtension($ext);

    /**
     * Find Classes
     *
     * @param string $subDir
     * @param string $suffix
     * @param string $parent
     * @return array
     */
    public function findClasses($subDir = null, $suffix = null, $parent = null);
}
