<?php

namespace Darsyn\ClassFinder\Tests\Reflection;

use Darsyn\ClassFinder\Reflection\ReflectionClass;

/**
 * @author Zander Baldwin <hello@zanderbaldwin.com>
 */
class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialization()
    {
        $reflect = new ReflectionClass(__CLASS__, '');
        $this->assertInstanceOf('\\ReflectionClass', $reflect);
        $this->setExpectedException('InvalidArgumentException');
        $reflect = new ReflectionCLass(__CLASS__, null);
    }

    public function testRelativePositionProcessing()
    {
        $reflect = new ReflectionClass(__CLASS__, '\\Tests\\Fixtures/Reflection/');
        $this->assertEquals('Tests/Fixtures/Reflection', $reflect->getRelativeDirectory());
        $this->assertEquals('Tests\\Fixtures\\Reflection', $reflect->getRelativeNamespace());
    }
}
