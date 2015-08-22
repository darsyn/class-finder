<?php

namespace Darsyn\ClassFinder\Tests;

use Darsyn\ClassFinder\ClassFinder;
use Symfony\Component\Finder\Finder;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class ClassFinderTest extends ArrayContentsAssertion
{
    /**
     * Directory
     *
     * @test
     * @access public
     * @return void
     */
    public function directory()
    {
        $finder = new ClassFinder;
        $directory = __DIR__;
        $this->assertAttributeEquals(null, 'directory', $finder);
        $this->assertEquals($finder, $finder->setRootDirectory($directory));
        $this->assertAttributeEquals($directory, 'directory', $finder);
        $this->assertEquals($directory, $finder->getRootDirectory());
    }

    /**
     * Namespace
     *
     * @test
     * @access public
     * @return void
     */
    public function testNamespace()
    {
        $finder = new ClassFinder;
        $namespace = __NAMESPACE__;
        $this->assertAttributeEquals(null, 'namespace', $finder);
        $this->assertEquals($finder, $finder->setRootNamespace($namespace));
        $this->assertAttributeEquals($namespace, 'namespace', $finder);
        $this->assertEquals($namespace, $finder->getRootNamespace());
    }

    /**
     * Extension
     *
     * @test
     * @access public
     * @return void
     */
    public function extension()
    {
        $finder = new ClassFinder;
        $this->assertAttributeEquals('.php', 'extension', $finder);
        $ext = '.txt';
        $this->assertEquals($finder, $finder->setExtension($ext));
        $this->assertAttributeEquals($ext, 'extension', $finder);
        $this->assertEquals($ext, $finder->getExtension());

        $ext = 'txt';
        $finder->setExtension($ext);
        $this->assertAttributeEquals('.' . $ext, 'extension', $finder);
        $this->assertEquals('.' . $ext, $finder->getExtension());
    }

    /**
     * Non-existent Directory
     *
     * @test
     * @access public
     * @return void
     */
    public function nonExistentDirectory()
    {
        $finder = new ClassFinder;
        $this->setExpectedException('DomainException');
        $finder->setRootDirectory(__DIR__ . '/non/existent/directory');
    }

    /**
     * Empty Namespace
     *
     * @test
     * @access public
     * @return void
     */
    public function emptyNamespace()
    {
        $finder = new ClassFinder();
        $finder->setRootDirectory(__DIR__);
        $this->assertEquals([], $finder->findClasses());
    }

    /**
     * Bare Class Finder
     *
     * @test
     * @access public
     * @return void
     */
    public function bareClassFinder()
    {
        $finder = new ClassFinder;
        // We have to search in the Fixtures directory because PHPUnit will double load the TestCases if we find them.
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\TestKernel',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\TestBundle',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Entity\\MyEntity',
        ], $finder->findClasses());
    }

    /**
     * Alternative Extension
     *
     * @test
     * @access public
     * @return void
     */
    public function alternativeExtension()
    {
        // For this test we are using a non-standard file extension that Composer does *NOT* automatically include
        // for us, we have to do it manually.
        require_once __DIR__ . '/Fixtures/Bundle/Module/MyTestModule.inc.php';
        $finder = new ClassFinder;
        // We have to search in the Fixtures directory because PHPUnit will double load the TestCases if we find them.
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $finder->setExtension('inc.php');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Module\\MyTestModule',
        ], $classes = $finder->findClasses());

        $finder->setExtension('.inc.php');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Module\\MyTestModule',
        ], $classes = $finder->findClasses());
    }

    /**
     * Sub-directory
     *
     * @test
     * @access public
     * @return void
     */
    public function subDirectory()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses('Controllers'));
    }

    /**
     * Suffix
     *
     * @test
     * @access public
     * @return void
     */
    public function suffix()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(null, 'Controller'));
    }

    /**
     * Sub-directory and Suffix
     *
     * @test
     * @access public
     * @return void
     */
    public function subDirectoryAndSuffix()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses('Controllers', 'Controller'));
        $this->assertSameArrayContents([], $finder->findClasses('Controller', 'Controller'));
        $this->assertSameArrayContents([], $finder->findClasses('Controller', 'Controllers'));
    }

    /**
     * Non-standard Extension and Suffix
     *
     * @test
     * @access public
     * @return void
     */
    public function nonStandardExtensionAndSuffix()
    {
        // For this test we are using a non-standard file extension that Composer does *NOT* automatically include
        // for us, we have to do it manually.
        require_once __DIR__ . '/Fixtures/Bundle/Module/MyTestModule.inc.php';
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $finder->setExtension('.inc.php');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Module\\MyTestModule',
        ], $finder->findClasses(null, 'Module'));
        $this->assertSameArrayContents([], $finder->findClasses(null, 'Controller'));
    }

    /**
     * Root and Subdir Do The Same Thing
     *
     * @test
     * @access public
     * @return void
     */
    public function rootAndSubDirDoTheSameThing()
    {
        $relative = 'Bundle';

        $relativeFinder = new ClassFinder;
        $relativeFinder->setRootDirectory(__DIR__ . '/Fixtures');
        $relativeFinder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $rootFinder = new ClassFinder;
        $rootFinder->setRootDirectory(__DIR__ . '/Fixtures/' . $relative);
        $rootFinder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\' . $relative);

        $expected = [
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\TestBundle',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Entity\\MyEntity',
        ];
        $this->assertSameArrayContents($expected, $rootFinder->findClasses());
        $this->assertSameArrayContents($expected, $relativeFinder->findClasses($relative));
    }

    /**
     * Parent
     *
     * @test
     * @access public
     * @return void
     */
    public function parent()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\TestKernel',
        ], $finder->findClasses(null, null, 'Symfony\\Component\\HttpKernel\\Kernel'));
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\TestKernel',
        ], $finder->findClasses(null, null, 'Symfony\\Component\\HttpKernel\\KernelInterface'));
    }

    /**
     * Parent and Suffix
     *
     * @test
     * @access public
     * @return void
     */
    public function parentAndSuffix()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(null, 'Controller', 'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'));
    }

    /**
     * Parent and Subdir
     *
     * @test
     * @access public
     * @return void
     */
    public function parentAndSubDir()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses('Controllers', null, 'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'));
    }

    /**
     * Parent, Subdir and Suffix
     *
     * @test
     * @access public
     * @return void
     */
    public function parentSubDirAndSuffix()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertSameArrayContents([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(
            'Controllers',
            'Controller',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'
        ));
    }

    /**
     * Allowed Parameters Setting Works
     *
     * @test
     * @access public
     * @return void
     */
    public function allowedParametersSettingWorks()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $this->assertCount(0, $finder->findClasses('Bundle/Module'));
        $this->assertCount(1, $finder->findClasses('Bundle/Module', null, null, 1));
    }

    /**
     * Reflection Objects Are Returned
     *
     * @test
     * @access public
     * @return void
     */
    public function reflectionObjectsAreReturned()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $classStrings = $finder->findClasses();
        $classReflections = $finder->findClassReflections();
        $this->assertCount(5, $classReflections);
        $this->assertTrue(count($classStrings) === count($classReflections));
        $this->assertContainsOnlyInstancesOf('ReflectionClass', $classReflections);
    }

    /**
     * Abstract Types Are Not Returned
     *
     * @test
     * @access public
     * @return void
     */
    public function abstractTypesAreNotReturned()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $this->assertCount(0, $finder->findClasses(null, 'Interface'));
    }
}
