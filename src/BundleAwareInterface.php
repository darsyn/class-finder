<?php

namespace Darsyn\ClassFinder;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
interface BundleAwareInterface extends ClassFinderInterface
{
    /**
     * Set Bundles
     *
     * @param array $bundles
     * @return void
     */
    public function setBundles(array $bundles);
}
