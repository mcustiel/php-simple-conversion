<?php
namespace Unit;

use Mcustiel\PhpSimpleConversion\ConverterBuilder;
use Fixtures\AToBConverter;
use Fixtures\B;
use Fixtures\A;

class ConverterBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldImplementFluentInterface()
    {
        $builder = ConverterBuilder::get();
        $this->assertInstanceOf(ConverterBuilder::class, $builder);
        $builder = $builder->from(A::class);
        $this->assertInstanceOf(ConverterBuilder::class, $builder);
        $builder = $builder->to(B::class);
        $this->assertInstanceOf(ConverterBuilder::class, $builder);
        $builder = $builder->withImplementation(AToBConverter::class);
    }

    /**
     * @expectedException         \InvalidArgumentException
     * @expectedExceptionMesesage 'From' parameter should be a string containing 'string', 'array', or a class name
     */
    public function testSetFromWithInvalidValue()
    {
        $builder = ConverterBuilder::get();
        $builder->from(5);
    }

    /**
     * @expectedException         \InvalidArgumentException
     * @expectedExceptionMesesage 'To' parameter should be a string containing 'string', 'array', or a class name
     */
    public function testSetToWithInvalidValue()
    {
        $builder = ConverterBuilder::get();
        $builder->to(5);
    }
}
