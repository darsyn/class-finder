<?php

namespace Darsyn\ClassFinder\Tests;

use Darsyn\ClassFinder\ClassFinder;
use Symfony\Component\Finder\Finder;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class ClassFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testDirectory()
    {
        $finder = new ClassFinder;
        $directory = __DIR__;
        $this->assertAttributeEquals(null, 'directory', $finder);
        $this->assertEquals($finder, $finder->setRootDirectory($directory));
        $this->assertAttributeEquals($directory, 'directory', $finder);
        $this->assertEquals($directory, $finder->getRootDirectory());
    }

    public function testNamespace()
    {
        $finder = new ClassFinder;
        $namespace = __NAMESPACE__;
        $this->assertAttributeEquals(null, 'namespace', $finder);
        $this->assertEquals($finder, $finder->setRootNamespace($namespace));
        $this->assertAttributeEquals($namespace, 'namespace', $finder);
        $this->assertEquals($namespace, $finder->getRootNamespace());
    }

    public function testExtension()
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

    public function testNonExistentDirectory()
    {
        $finder = new ClassFinder;
        $this->setExpectedException('DomainException');
        $finder->setRootDirectory(__DIR__ . '/non/existent/directory');
    }

    public function testEmptyNamespace()
    {
        $finder = new ClassFinder();
        $finder->setRootDirectory(__DIR__);
        $this->assertEquals([], $finder->findClasses());
    }

    public function testBareClassFinder()
    {
        $finder = new ClassFinder;
        // We have to search in the Fixtures directory because PHPUnit will double load the TestCases if we find them.
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\TestKernel',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\TestBundle',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Entity\\MyEntity',
        ], $finder->findClasses());
    }

    public function testAlternativeExtension()
    {
        // For this test we are using a non-standard file extension that Composer does *NOT* automatically include
        // for us, we have to do it manually.
        require_once __DIR__ . '/Fixtures/Bundle/Module/MyTestModule.inc.php';
        $finder = new ClassFinder;
        // We have to search in the Fixtures directory because PHPUnit will double load the TestCases if we find them.
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $finder->setExtension('inc.php');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Module\\MyTestModule',
        ], $classes = $finder->findClasses());

        $finder->setExtension('.inc.php');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Module\\MyTestModule',
        ], $classes = $finder->findClasses());
    }

    public function testSubDirectory()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses('Controllers'));
    }

    public function testSuffix()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(null, 'Controller'));
    }

    public function testSubDirectoryAndSuffix()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses('Controllers', 'Controller'));
        $this->assertEquals([], $finder->findClasses('Controller', 'Controller'));
        $this->assertEquals([], $finder->findClasses('Controller', 'Controllers'));
    }

    public function testNonStandardExtensionAndSuffix()
    {
        // For this test we are using a non-standard file extension that Composer does *NOT* automatically include
        // for us, we have to do it manually.
        require_once __DIR__ . '/Fixtures/Bundle/Module/MyTestModule.inc.php';
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $finder->setExtension('.inc.php');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Module\\MyTestModule',
        ], $finder->findClasses(null, 'Module'));
        $this->assertEquals([], $finder->findClasses(null, 'Controller'));
    }

    public function testRootAndSubDirDoTheSameThing()
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
        $this->assertEquals($expected, $rootFinder->findClasses());
        $this->assertEquals($expected, $relativeFinder->findClasses($relative));
    }

    public function testParent()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\TestKernel',
        ], $finder->findClasses(null, null, 'Symfony\\Component\\HttpKernel\\Kernel'));
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\TestKernel',
        ], $finder->findClasses(null, null, 'Symfony\\Component\\HttpKernel\\KernelInterface'));
    }

    public function testParentAndSuffix()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(null, 'Controller', 'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'));
    }

    public function testParentAndSubDir()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses('Controllers', null, 'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'));
    }

    public function testParentSubDirAndSuffix()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures/Bundle');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures\\Bundle');
        $this->assertEquals([
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\SecondaryController',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\Bundle\\Controllers\\DefaultController',
        ], $finder->findClasses(
            'Controllers',
            'Controller',
            'Darsyn\\ClassFinder\\Tests\\Fixtures\\ControllerInterface'
        ));
    }

    public function testAllowedParametersSettingWorks()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $this->assertCount(0, $finder->findClasses('Bundle/Module'));
        $this->assertCount(1, $finder->findClasses('Bundle/Module', null, null, 1));
    }

    public function testReflectionObjectsAreReturned()
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

    public function testAbstractTypesAreNotReturned()
    {
        $finder = new ClassFinder;
        $finder->setRootDirectory(__DIR__ . '/Fixtures');
        $finder->setRootNamespace(__NAMESPACE__ . '\\Fixtures');
        $this->assertCount(0, $finder->findClasses(null, 'Interface'));
    }
}
