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
use Mcustiel\Conversion\ConverterBuilder;
use Mcustiel\Conversion\SingletonConverterContainer;

class ConverterContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldAddAConverterBuilderAndGetItCorrectly()
    {
        $builder = ConverterBuilder::get()
            ->from(A::class)
            ->to(B::class)
            ->withImplementation(AToBConverter::class);
        SingletonConverterContainer::getInstance()
            ->addConverter($builder);

        $this->assertInstanceOf(
            AToBConverter::class,
            SingletonConverterContainer::getInstance()->getConverter(A::class, B::class)
        );
    }

    public function testShouldFailWithoutFrom()
    {
        $builder = ConverterBuilder::get()
            ->to(B::class)
            ->withImplementation(AToBConverter::class);
        SingletonConverterContainer::getInstance()
            ->addConverter($builder);
    }

    public function testShouldFailWithoutTo()
    {
        $builder = ConverterBuilder::get()
            ->from(A::class)
            ->withImplementation(AToBConverter::class);
        SingletonConverterContainer::getInstance()
            ->addConverter($builder);
    }

    public function testShouldFailIfConverterNotRegistered()
    {
        SingletonConverterContainer::getInstance()->getConverter(\SplFileObject::class, \stdClass::class);
    }

    public function testShouldFailWhenImplementationIsNotConverter()
    {
        $builder = ConverterBuilder::get()
            ->from(A::class)
            ->to(B::class)
            ->withImplementation(\stdClass::class);
        SingletonConverterContainer::getInstance()
            ->addConverter($builder);

        SingletonConverterContainer::getInstance()->getConverter(A::class, B::class);
    }
}
