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
     * Find Class Reflections
     *
     * @access public
     * @param string $subDir
     * @param string $suffix
     * @param string $parent
     * @param integer $allowedParameters
     * @return array
     */
    public function findClassReflections($subDir = null, $suffix = null, $parent = null, $allowedParameters = 0)
    {
        $classes = [];
        $subDir = trim(preg_replace('#//{2,}#', '/', strtr($subDir, '\\', '/')), '/');
        $namespace = trim($this->namespace, '\\') . '\\' . strtr($subDir, '/', '\\') . '\\';
        $finder = $this->directory !== null
               && is_dir($directory = $this->directory . DIRECTORY_SEPARATOR . strtr($subDir, '/', DIRECTORY_SEPARATOR))
            ? (new Finder)->files()->name(sprintf('*%s', $this->extension))->in($directory)
            : [];
        foreach ($finder as $file) {
            try {
                $class = $this->getClassReflection($file, $namespace, $suffix, $parent, $allowedParameters);
                $classes[] = $class;
            } catch (\Exception $exception) {
                continue;
            }
        }
        return $classes;
    }

    /**
     * Find Classes
     *
     * @access public
     * @param string $subDir
     * @param string $suffix
     * @param string $parent
     * @param integer $allowedParameters
     * @return \Darsyn\ClassFinder\Reflection\Class|array
     */
    public function findClasses($subDir = null, $suffix = null, $parent = null, $allowedParameters = 0)
    {
        return array_map(function (ReflectionClass $class) {
            return $class->getName();
        }, $this->findClassReflections($subDir, $suffix, $parent, $allowedParameters));
    }

    /**
     * Get Class Reflection Instance
     *
     * @access protected
     * @throws \LogicException
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @param string $namespace
     * @param string $suffix
     * @param string $parent
     * @param integer $allowedParameters
     * @return \Darsyn\ClassFinder\Reflection\ReflectionClass
     */
    protected function getClassReflection(SplFileInfo $file, $namespace, $suffix, $parent, $allowedParameters)
    {
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
        // - For this to work the constructor must not have more than the expected number of required parameters.
        // - And finally make sure that the class loaded was actually loaded from the directory we found it in.
        //   TODO: Make sure that the final (file path) check doesn't cause problems with proxy classes or
        //         bootstraped/compiled class caches.
        $reflect = new ReflectionClass($class, $file->getRelativePath());
        if ((is_object($construct = $reflect->getConstructor())
                && $construct->getNumberOfRequiredParameters() > $allowedParameters
            )
            || $reflect->isAbstract() || $reflect->isInterface() || $reflect->isTrait()
            || (is_string($parent) && !empty($parent) && !$reflect->isSubclassOf($parent))
            || $reflect->getFileName() !== $file->getRealPath()
        ) {
            throw new \LogicException(sprintf('The class definition for "%s" is invalid.', $class));
        }
        return $reflect;
    }
}
