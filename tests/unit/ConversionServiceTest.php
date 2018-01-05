<?php
/**
 * This file is part of php-simple-conversion.
 *
 * php-simple-conversion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * php-simple-conversion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with php-simple-conversion.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Unit;

use Fixtures\C;
use Mcustiel\Conversion\ConverterContainer;
use Mcustiel\Conversion\ConverterBuilder;
use Fixtures\A;
use Fixtures\AToBConverter;
use Fixtures\B;
use Mcustiel\Conversion\ConversionService;
use Mcustiel\Conversion\Converter;
use Mcustiel\Conversion\Exception\ConverterDoesNotExistException;

class ConversionServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConversionService
     */
    private $service;
    /**
     * @var ConverterContainer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $containerMock;
    /**
     * @var Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $converterMock;

    public function setUp()
    {
        $this->converterMock = $this->getMockBuilder(Converter::class)
            ->disableOriginalConstructor()
            ->getMock();
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
        $toConvert = new A(1, '');

        $this->containerMock
            ->expects($this->once())
            ->method('getConverter')
            ->with($this->equalTo(A::class), $this->equalTo(B::class))
            ->will($this->returnValue($this->converterMock));

        $this->converterMock
            ->expects($this->once())
            ->method('convert')
            ->with($toConvert);

        $this->service->convert($toConvert, B::class);
    }

    public function testShouldCallConverterToConvertAString()
    {
        $this->containerMock
            ->expects($this->once())
            ->method('getConverter')
            ->with($this->equalTo('string'), $this->equalTo(B::class))
            ->will($this->returnValue($this->converterMock));

        $this->converterMock
            ->expects($this->once())
            ->method('convert')
            ->with('aString');

        $this->service->convert('aString', B::class);
    }

    public function testShouldCallConverterToConvertAnArray()
    {
        $this->containerMock
            ->expects($this->once())
            ->method('getConverter')
            ->with($this->equalTo('array'), $this->equalTo(B::class))
            ->will($this->returnValue($this->converterMock));

        $this->converterMock
            ->expects($this->once())
            ->method('convert')
            ->with([]);

        $this->service->convert([], B::class);
    }

    public function testShouldThrowExceptionWhenCalledWithInvalidFromType()
    {
        $this->setExpectedException(
            TryingInvalidConversionException::class,
            "Trying to convert from 'integer'. Can only convert from string, array or object"
        );
        $this->service->convert(5, B::class);
    }

    public function testIfConvertsFromTheParentClass()
    {
        $toConvert = new C(1, '');

        $this->containerMock
            ->expects($this->exactly(2))
            ->method('getConverter')
            ->withConsecutive(
                [$this->equalTo(C::class), $this->equalTo(B::class)],
                [$this->equalTo(A::class), $this->equalTo(B::class)]
            )
            ->will(
                $this->returnCallback(function ($from) {
                    if ($from === C::class) {
                        throw new ConverterDoesNotExistException('');
                    }
                    return $this->converterMock;
                })
            );

        $expected = new B();
        $this->converterMock
            ->expects($this->once())
            ->method('convert')
            ->with($toConvert)
            ->willReturn($expected);

        $this->assertSame($expected, $this->service->convert($toConvert, B::class, true));
    }

    /**
     * @expectedException \Mcustiel\Conversion\Exception\ConverterDoesNotExistException
     */
    public function testIfFailsWhenConvertingFromParentNotAllowed()
    {
        $toConvert = new C(1, '');

        $this->containerMock
            ->expects($this->once())
            ->method('getConverter')
            ->with($this->equalTo(C::class), $this->equalTo(B::class))
            ->will($this->throwException(new ConverterDoesNotExistException('')));

        $this->service->convert($toConvert, B::class);
    }

    /**
     * @expectedException \Mcustiel\Conversion\Exception\ConverterDoesNotExistException
     * @expectedExceptionMessage Converter from Fixtures\C to Fixtures\A does not exist
     */
    public function testIfNoConverterForParentClass()
    {
        $toConvert = new C(1, '');

        $this->containerMock
            ->expects($this->exactly(2))
            ->method('getConverter')
            ->withConsecutive(
                [$this->equalTo(C::class), $this->equalTo(A::class)],
                [$this->equalTo(A::class), $this->equalTo(A::class)]
            )
            ->willThrowException(new ConverterDoesNotExistException(''));
        $this->service->convert($toConvert, A::class, true);
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
