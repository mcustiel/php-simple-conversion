<?php
namespace Integration\PhpSimpleConversion;

use Mcustiel\PhpSimpleConversion\ConverterContainer;
use Mcustiel\PhpSimpleConversion\ConverterBuilder;
use Fixtures\A;
use Fixtures\B;
use Fixtures\AToBConverter;
use Mcustiel\PhpSimpleConversion\Converter;
use Mcustiel\PhpSimpleConversion\ConversionService;

class ConversionTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \Mcustiel\PhpSimpleConversion\ConverterContainer
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
     * @expectedException        \Mcustiel\PhpSimpleConversion\Exception\ObjectIsNotConverterException
     * @expectedExceptionMessage Object of type stdClass does not implement Mcustiel\PhpSimpleConversion\Converter
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
