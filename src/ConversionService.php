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

namespace Mcustiel\Conversion;

use Mcustiel\Conversion\Exception\ConverterDoesNotExistException;
use Mcustiel\Conversion\Exception\TryingInvalidConversionException;

/**
 * Service class for PhpSimpleConversion. It's used to access all the service
 * provided by the library, which basically are register converters and do conversions.
 *
 * @author mcustiel
 */
class ConversionService
{
    const ALLOW_PARENTS = true;

    /**
     * @var ConverterContainer
     */
    private $container;

    /**
     * @param SingletonConverterContainer $container
     */
    public function __construct(ConverterContainer $container = null)
    {
        $this->container = $container ?: SingletonConverterContainer::getInstance();
    }

    /**
     * Converts a given object to another type.
     *
     * @param string|array|object $object         the object to convert
     * @param string              $toClass        The name of the class to which the object will be converted
     * @param bool                $iterateParents Whether or not to search for a converter for the parent class
     *
     * @throws ConverterDoesNotExistException
     */
    public function convert($object, $toClass, $iterateParents = false)
    {
        $from = $this->getTypeOf($object);
        $originalType = $from;
        do {
            try {
                $converter = $this->container->getConverter($from, $toClass);

                return $converter->convert($object);
            } catch (ConverterDoesNotExistException $e) {
                $from = $this->manageException($iterateParents, $from, $e);
            }
        } while ($from);

        throw new ConverterDoesNotExistException(
            "Converter from {$originalType} to {$toClass} does not exist"
        );
    }

    /**
     * Registers a converter in the library for future use.
     *
     * @param ConverterBuilder $builder The converter builder that creates the converter to register
     */
    public function registerConverter(ConverterBuilder $builder)
    {
        $this->container->addConverter($builder);
    }

    /**
     * @param bool       $iterateParents
     * @param string     $from
     * @param \Exception $e
     */
    private function manageException($iterateParents, $from, $e)
    {
        if (!$iterateParents) {
            throw $e;
        } elseif (class_exists($from)) {
            $from = get_parent_class($from);
        }

        return $from;
    }

    /**
     * @param mixed $object
     *
     * @throws TryingInvalidConversionException
     *
     * @return string
     */
    private function getTypeOf($object)
    {
        $type = gettype($object);
        if ($type === 'string' || $type === 'array') {
            return $type;
        }
        if ($type === 'object') {
            return get_class($object);
        }
        throw new TryingInvalidConversionException(
            "Trying to convert from '{$type}'. Can only convert from string, array or object"
        );
    }
}
