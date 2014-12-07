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
namespace Mcustiel\PhpSimpleConversion;

use Mcustiel\PhpSimpleConversion\Exception\TryingInvalidConversionException;

class ConversionService
{
    /**
     *
     * @var ConverterContainer
     */
    private $container;

    public function __construct(ConverterContainer $container = null)
    {
        $this->container = $container === null ?
            ConverterContainer::getInstance() : $container;
    }

    public function convert($object, $toClass)
    {
        $from = $this->getTypeOf($object);
        $converter = $this->container->getConverter($from, $toClass);

        return $converter->convert($object);
    }

    private function getTypeOf($object)
    {
        $type = gettype($object);
        switch ($type) {
            case 'string':
            case 'array':
                return $type;
            case 'object':
                return get_class($object);
            default:
                throw new TryingInvalidConversionException(
                    "Trying to convert from '{$type}'. Can only convert from string, array or object"
                );
        }
    }

    public function registerConverter(ConverterBuilder $builder)
    {
        $this->container->addConverter($builder);
    }
}
