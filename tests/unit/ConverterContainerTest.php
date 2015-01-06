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

use Mcustiel\Conversion\ConverterBuilder;
use Fixtures\A;
use Fixtures\B;
use Fixtures\AToBConverter;
use Mcustiel\Conversion\ConverterContainer;
use Mcustiel\Conversion\ConversionService;

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
     * @expectedException        \Mcustiel\Conversion\Exception\ConverterDoesNotExistException
     * @expectedExceptionMessage Converter from FileObject to stdClass does not exist
     */
    public function testShouldFailIfConverterNotRegistered()
    {
        ConverterContainer::getInstance()->getConverter(\FileObject::class, \stdClass::class);
    }

    /**
     * @expectedException        \Mcustiel\Conversion\Exception\ObjectIsNotConverterException
     * @expectedExceptionMessage Object of type stdClass does not implement Mcustiel\Conversion\Converter
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
