<?php
namespace Integration;

use Mcustiel\PhpSimpleConversion\ConversionService;
use Mcustiel\PhpSimpleConversion\ConverterBuilder;
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
