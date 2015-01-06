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

use Mcustiel\Conversion\ConverterContainer;
use Mcustiel\Conversion\ConverterBuilder;
use Fixtures\A;
use Fixtures\B;
use Fixtures\AToBConverter;
use Mcustiel\Conversion\Converter;
use Mcustiel\Conversion\ConversionService;

class ConversionTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Mcustiel\Conversion\ConverterContainer
     */
    private $conversionService;

    public function setUp()
    {
        $this->conversionService = ConverterContainer::getInstance();
        $builder = ConverterBuilder::get()->from(A::class)
            ->to(B::class)
            ->withImplementation(AToBConverter::class);
        $this->conversionService->addConverter($builder);
    }

    public function testIfConverterContainerSavesAndReturnsCorrectly()
    {
        $this->assertInstanceOf(Converter::class,
            $this->conversionService->getConverter(A::class, B::class));
    }

    public function testIfConverterWorksCorrectlyWhenCalled()
    {
        $a = $this->buildAClass();
        $converter = $this->conversionService->getConverter(A::class, B::class);
        $b = $converter->convert($a);

        $this->assertBIsCorrect($b);
    }

    public function testConverterUsingWrapper()
    {
        $a = $this->buildAClass();

        $service = new ConversionService();
        $b = $service->convert($a, B::class);

        $this->assertBIsCorrect($b);
    }

    /**
     * @expectedException        \Mcustiel\Conversion\Exception\ObjectIsNotConverterException
     * @expectedExceptionMessage Object of type stdClass does not implement Mcustiel\Conversion\Converter
     */
    public function testShouldThrowAnExceptionWhenImplementationIsNotConverter()
    {
        $this->conversionService = ConverterContainer::getInstance();
        $builder = ConverterBuilder::get()
            ->from(B::class)
            ->to(A::class)
            ->withImplementation(\stdClass::class);
        $this->conversionService->addConverter($builder);
        $converter = $this->conversionService->getConverter(B::class, A::class);
    }

    private function assertBIsCorrect($b)
    {
        $this->assertEquals(1, $b->getId());
        $this->assertEquals('john', $b->getFirstName());
        $this->assertEquals('doe', $b->getLastName());
        $this->assertEquals(30, $b->getAge());
    }

    private function buildAClass()
    {
        $a = new A(1,
            json_encode(
                array(
                    'firstName' => 'john',
                    'lastName' => 'doe',
                    'age' => 30
                )));
        return $a;
    }
}
