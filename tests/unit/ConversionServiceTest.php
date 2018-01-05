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

use Fixtures\A;
use Fixtures\AToBConverter;
use Fixtures\B;
use Mcustiel\Conversion\ConversionService;
use Mcustiel\Conversion\Converter;
use Mcustiel\Conversion\ConverterBuilder;
use Mcustiel\Conversion\ConverterContainer;

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
