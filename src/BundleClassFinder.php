<?php

namespace Darsyn\ClassFinder;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class BundleClassFinder extends ClassFinder implements BundleAwareInterface
{
    /**
     * @access protected
     * @var array
     */
    protected $bundles;

    /**
     * Constructor
     *
     * @access public
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel = null)
    {
        if ($kernel instanceof KernelInterface) {
            $this->setBundles($kernel->getBundles());
        }
    }

    /**
     * Set Bundles
     *
     * @access public
     * @param array $bundles
     * @return self
     */
    public function setBundles(array $bundles)
    {
        $this->bundles = $bundles;
        return $this;
    }

    /**
     * Add Bundle
     *
     * @access public
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @return self
     */
    public function addBundle(BundleInterface $bundle)
    {
        $this->bundles[] = $bundle;
        return $this;
    }

    /**
     * Find Classes in Bundles
     *
     * @access public
     * @param string $subDir
     * @param string $suffix
     * @param string $parent
     * @return array
     */
    public function findClasses($subDir = null, $suffix = null, $parent = null)
    {
        $classes = [];
        foreach ($this->bundles as $bundle) {
            $this->setRootDirectory($bundle->getPath());
            $this->setRootNamespace($bundle->getNamespace());
            $classes = array_merge($classes, parent::findClasses($subDir, $suffix, $parent));
        }
        return $classes;
    }
}
