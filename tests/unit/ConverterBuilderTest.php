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

    public function testSetFromWithInvalidValue()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            "'From' parameter should be a string containing 'string', 'array', or a class name"
        );
        $builder = ConverterBuilder::get();
        $builder->from(5);
    }

    public function testSetToWithInvalidValue()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            "'To' parameter should be a string containing a type name"
        );
        $builder = ConverterBuilder::get();
        $builder->to(5);
    }
}
