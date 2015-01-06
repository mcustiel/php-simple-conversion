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
namespace Integration;

use Mcustiel\Conversion\ConversionService;
use Mcustiel\Conversion\ConverterBuilder;
use Fixtures\AToBConverter;
use Fixtures\A;
use Fixtures\B;

class PerformanceTest extends \PHPUnit_Framework_TestCase
{
    public function testPerformance()
    {
        $service = new ConversionService();
        $builder = ConverterBuilder::get()
            ->from(A::class)
            ->to(B::class)
            ->withImplementation(AToBConverter::class);
        $service->registerConverter($builder);

        $a = new A(1, json_encode(
            array(
                'firstName' => 'john',
                'lastName' => 'doe',
                'age' => 30
        )));

        foreach ([5000, 15000, 25000, 50000] as $cycles) {
            $start = microtime(true);
            for ($i = $cycles; $i > 0; $i--) {
                $b = $service->convert($a, B::class);
            }
            echo "\n{$cycles} cycles executed in " . (microtime(true) - $start) . " seconds\n";
        }
    }
}
