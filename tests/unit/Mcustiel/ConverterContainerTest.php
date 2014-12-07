<?php
namespace Unit;

use Mcustiel\PhpSimpleConversion\ConverterBuilder;
use Fixtures\A;
use Fixtures\B;
use Fixtures\AToBConverter;
use Mcustiel\PhpSimpleConversion\ConverterContainer;
use Mcustiel\PhpSimpleConversion\ConversionService;

class ConverterContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldAddAConverterBuilderAndGetItCorrectly()
    {
        $builder = ConverterBuilder::get()
            ->from(A::class)
            ->to(B::class)
            ->withImplementation(AToBConverter::class);
        ConverterContainer::getInstance()
            ->addConverter($builder);

        $this->assertInstanceOf(
            AToBConverter::class,
            ConverterContainer::getInstance()->getConverter(A::class, B::class)
        );
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage From is unset
     */
    public function testShouldFailWithoutFrom()
    {
        $builder = ConverterBuilder::get()
            ->to(B::class)
            ->withImplementation(AToBConverter::class);
        ConverterContainer::getInstance()
            ->addConverter($builder);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage To is unset
     */
    public function testShouldFailWithoutTo()
    {
        $builder = ConverterBuilder::get()
            ->from(A::class)
            ->withImplementation(AToBConverter::class);
        ConverterContainer::getInstance()
            ->addConverter($builder);
    }

    /**
     * @expectedException        \Mcustiel\PhpSimpleConversion\Exception\ConverterDoesNotExistException
     * @expectedExceptionMessage Converter from FileObject to stdClass does not exist
     */
    public function testShouldFailIfConverterNotRegistered()
    {
        ConverterContainer::getInstance()->getConverter(\FileObject::class, \stdClass::class);
    }

    /**
     * @expectedException        \Mcustiel\PhpSimpleConversion\Exception\ObjectIsNotConverterException
     * @expectedExceptionMessage Object of type stdClass does not implement Mcustiel\PhpSimpleConversion\Converter
     */
    public function testShouldFailWhenImplementationIsNotConverter()
    {
        $builder = ConverterBuilder::get()
            ->from(A::class)
            ->to(B::class)
            ->withImplementation(\stdClass::class);
        ConverterContainer::getInstance()
            ->addConverter($builder);

        ConverterContainer::getInstance()->getConverter(A::class, B::class);
    }
}
