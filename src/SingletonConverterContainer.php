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

/**
 * Singleton class the holds all the registered converters, and allows to access them.
 *
 * @author mcustiel
 */
class SingletonConverterContainer implements ConverterContainer
{
    /**
     *
     * @var ConverterContainer
     */
    private static $instance;

    /**
     *
     * @var SimpleConverterContainer
     */
    private $container;

    /**
     * This is singleton, can't be instantiated directly.
     */
    private function __construct()
    {
        $this->container = new SimpleConverterContainer();
    }

    /**
     * Get the instance of this class.
     *
     * @return \Mcustiel\Conversion\ConverterContainer
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Registers a converter.
     *
     * @param ConverterBuilder $converter The builder of the converter to register.
     * @throws \InvalidArgumentException If from or to are unset.
     */
    public function addConverter(ConverterBuilder $converter)
    {
        $this->container->addConverter($converter);
    }

    /**
     * Access the implementation of the converter for the given from and to parameters.
     *
     * @param string $from The type from which the converter converts.
     * @param string $to   The type to which the converter converts to.
     *
     * @throws \Mcustiel\Conversion\Exception\ConverterDoesNotExistException
     *
     * @return Converter
     */
    public function getConverter($from, $to)
    {
        return $this->container->getConverter($from, $to);
    }
}
