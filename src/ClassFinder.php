<?php

namespace Darsyn\ClassFinder;

use Darsyn\ClassFinder\Reflection\ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class ClassFinder
{
    /**
     * @access protected
     * @var string
     */
    protected $directory;

    /**
     * @access protected
     * @var string
     */
    protected $namespace;

    /**
     * @access protected
     * @var string
     */
    protected $extension = '.php';

    /**
     * Set Root Directory
     *
     * @access public
     * @throws \DomainException
     * @param string $directory
     * @return self
     */
    public function setRootDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new \DomainException(sprintf(
                'The value "%s" defined as the root directory does not exist.',
                $directory
            ));
        }
        $this->directory = $directory;
        return $this;
    }

    /**
     * Get Root Directory
     *
     * @access public
     * @return string
     */
    public function getRootDirectory()
    {
        return $this->directory;
    }

    /**
     * Set Root Namespace
     *
     * @access public
     * @param string $namespace
     * @return self
     */
    public function setRootNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Get Root Namespace
     *
     * @access public
     * @return string
     */
    public function getRootNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set the file extension to search for (with leading full-stop).
     *
     * @access public
     * @param string $extension
     * @return self
     */
    public function setExtension($extension)
    {
        $this->extension = $extension !== null ? '.' . trim($extension, '.') : '';
        return $this;
    }

    /**
     * Get File Extension
     *
     * @access public
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Find Classes
     *
     * @access public
     * @param string $subDir
     * @param string $suffix
     * @param string $parent
     * @param boolean $reflection
     * @return \Darsyn\ClassFinder\Reflection\Class|array
     */
    public function findClasses($subDir = null, $suffix = null, $parent = null, $reflection = false)
    {
        $classes = [];
        $subDir = trim(preg_replace('#//{2,}#', '/', strtr($subDir, '\\', '/')), '/');
        $namespace = trim($this->namespace, '\\') . '\\' . strtr($subDir, '/', '\\') . '\\';
        if ($this->directory === null
            || !is_dir($directory = $this->directory . DIRECTORY_SEPARATOR . strtr($subDir, '/', DIRECTORY_SEPARATOR))
        ) {
            return $classes;
        }

        $finder = (new Finder)->files()->name(sprintf('*%s', $this->extension))->in($directory);
        foreach ($finder as $file) {
            try {
                $class = $this->getFullyQualifiedClassName($file, $namespace, $suffix, $parent, (bool) $reflection);
                $classes[] = $class;
            } catch (\Exception $exception) {
                continue;
            }
        }
        return $classes;
    }

    /**
     * Get Fully-Qualified Class Name
     *
     * @access protected
     * @throws \LogicException
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @param string $namespace
     * @param string $suffix
     * @param string $parent
     * @param boolean $reflection
     * @return \Darsyn\ClassFinder\Reflection\Class|string
     */
    protected function getFullyQualifiedClassName(
        SplFileInfo $file,
        $namespace,
        $suffix = null,
        $parent = null,
        $reflection = false
    ) {
        // Determine the fully-qualified class name of the found file.
        $class = preg_replace('#\\\\{2,}#', '\\', sprintf(
            '%s\\%s\\%s',
            $namespace,
            strtr($file->getRelativePath(), '/', '\\'),
            $file->getBasename($this->extension)
        ));
        // Make sure that the class name has the correct suffix.
        if (!empty($suffix) && substr($class, 0 - strlen($suffix)) !== $suffix) {
            throw new \LogicException(sprintf(
                'The file found at "%s" does not end with the required suffix of "%s".',
                $file->getRealPath(),
                $suffix
            ));
        }
        // We have to perform a few checks on the class before we can return it.
        // - It must be an actual class; not interface, abstract or trait types.
        // - For this to work the constructor must not have any required arguments.
        // - And finally make sure that the class loaded was actually loaded from the directory we found it in.
        //   TODO: Make sure that the final check doesn't cause problems with proxy classes.
        $reflect = new ReflectionClass($class, $file->getRelativePath());
        if ((is_object($construct = $reflect->getConstructor()) && $construct->getNumberOfRequiredParameters())
            || $reflect->isAbstract()
            || (is_string($parent) && !empty($parent) && !$reflect->isSubclassOf($parent))
            || $reflect->getFileName() !== $file->getRealPath()
        ) {
            throw new \LogicException(sprintf('The class definition for "%s" is invalid.', $class));
        }
        return $reflection ? $reflect : $class;
    }
}
