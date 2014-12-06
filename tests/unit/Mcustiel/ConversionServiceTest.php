<?php
namespace Unit;

use Mcustiel\PhpSimpleConversion\ConversionService;
use Mcustiel\PhpSimpleConversion\ConverterContainer;
use Mcustiel\PhpSimpleConversion\ConverterBuilder;
use Fixtures\A;
use Fixtures\B;
use Fixtures\AToBConverter;
use Mcustiel\PhpSimpleConversion\Converter;

class ConversionServiceTest extends \PHPUnit_Framework_TestCase
{
    private $service;
    private $containerMock;

    public function setUp()
    {
        $this->containerMock = $this->getMockBuilder(ConverterContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new ConversionService($this->containerMock);
    }

    public function testShouldRegisterAConverter()
    {
        $register = $this->getConverterBuilderToRegister();

        $this->containerMock
            ->expects($this->once())
            ->method('addConverter')
            ->with($register);
        $this->service->registerConverter($register);
    }

    public function testShouldCallConverter()
    {
        $converterMock = $this->getMockBuilder(Converter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $toConvert = new A(1, '');

        $this->containerMock
            ->expects($this->once())
            ->method('getConverter')
            ->with($this->equalTo(A::class), $this->equalTo(B::class))
            ->will($this->returnValue($converterMock));

        $converterMock
            ->expects($this->once())
            ->method('convert')
            ->with($toConvert);

        $this->service->convert($toConvert, B::class);
    }

    public function testShouldCallConverterToConvertAString()
    {
        $converterMock = $this->getMockBuilder(Converter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock
            ->expects($this->once())
            ->method('getConverter')
            ->with($this->equalTo('string'), $this->equalTo(B::class))
            ->will($this->returnValue($converterMock));

        $converterMock
            ->expects($this->once())
            ->method('convert')
            ->with('aString');

        $this->service->convert('aString', B::class);
    }

    public function testShouldCallConverterToConvertAnArray()
    {
        $converterMock = $this->getMockBuilder(Converter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock
            ->expects($this->once())
            ->method('getConverter')
            ->with($this->equalTo('array'), $this->equalTo(B::class))
            ->will($this->returnValue($converterMock));

        $converterMock
            ->expects($this->once())
            ->method('convert')
            ->with([]);

        $this->service->convert([], B::class);
    }

    /**
     * @expectedException        \Mcustiel\PhpSimpleConversion\Exception\TryingInvalidConversionException
     * @expectedExceptionMessage Trying to convert from 'integer'. Can only convert from string, array or object
     */
    public function testShouldThrowExceptionWhenCalledWithInvalidFromType()
    {
        $this->service->convert(5, B::class);
    }


    private function getConverterBuilderToRegister()
    {
        $register = ConverterBuilder::get()
            ->from(A::class)
            ->to(B::class)
            ->withImplementation(AToBConverter::class);
        return $register;
    }
}
