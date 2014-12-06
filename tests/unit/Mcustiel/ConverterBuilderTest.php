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
}
