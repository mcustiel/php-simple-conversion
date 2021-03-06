php-simple-conversion
=====================

What is it?
-----------

php-simple-conversion is a minimalistic conversion service for PHP. It's meant to be performant and easy to use. It allows developers to register a series of converter classes that converts from one type to another. The converters are instantiated only when they are needed, minimizing memory use and avoiding unnecessary processing.

[![Build Status](https://scrutinizer-ci.com/g/mcustiel/php-simple-conversion/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mcustiel/php-simple-conversion/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/mcustiel/php-simple-conversion/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mcustiel/php-simple-conversion/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mcustiel/php-simple-conversion/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mcustiel/php-simple-conversion/?branch=master)

Installation
------------

### Composer:

This project is published in packagist, so you just need to add it as a dependency in your composer.json:

```javascript
"require": {
        // ...
        "mcustiel/php-simple-conversion": "*"
    }
```

Or just download the release and include it in your path.

How to use it?
--------------

### Define your converters

First of all, you have to define the converters you will use. As an example, supose you have two classes that represents a Person: one of them represents it in the format it is persisted in db, the other represents it as used in application's logic.

```php
class DatabaseRegisterRepresentationForPerson 
{
    private $id;
    private $jsonString;

    public function __construct($id, $jsonString)
    {
        $this->id = $id;
        $this->jsonString = $jsonString;
    }
    // ... Getters and setters
}

class DomainRepresentationForPerson
{
    private $id;
    private $firstName;
    private $lastName;
    private $age;
    
    // ... Getters and setters
}
```

You need a service that abstracts the access to database from the logic, in that service you will use a converter to convert from the class returned from the DAO to the class used by the logic and return it. Following there is an example of the converter your service should use. 

```php
use Mcustiel\PhpSimpleConversion\Converter;

// class DBPersonToLogicPersonConverter (MUST implement Converter interface)
class DBPersonToLogicPersonConverter implements Converter
{
    public function convert($a)
    {
        if (! ($a instanceof DatabaseRegisterRepresentationForPerson)) {
            throw new \InvalidArgumentException("Should convert only from DatabaseRegisterRepresentationForPerson");
        }
        $return = new DomainRepresentationForPerson();

        $return->setId($a->getId());
        $object = json_decode($a->getJsonString());
        $return->setFirstName($object->firstName);
        $return->setLastName($object->lastName);
        $return->setAge($object->age);

        return $return;
    }
}
```
You can define all the converters you want and then register them.

### Registering

In your bootstrap file (or some startup script) you must register all the converters you have defined.

```php
use Mcustiel\PhpSimpleConversion\ConversionService;
use Mcustiel\PhpSimpleConversion\ConverterBuilder;

$conversionService = new ConversionService();
// ...
$converter = ConverterBuilder::get()
    ->from(DatabaseRegisterRepresentationForPerson::class)
    ->to(DomainRepresentationForPerson::class)
    ->withImplementation(DBPersonToLogicPersonConverter::class); // Implementation could be the name of the class or an instance
$conversionService->registerConverter($converter);
```

### Convert

Then you just need to inject the ConversionService to any class where you want to do conversions and call it as follows:

```php
    $dbPerson = $personDao->getPerson('alice');
    $logicPerson = $conversionService->convert($dbPerson, DomainRepresentationForPerson::class);
```

The library will automatically take care of resolving the registered service and calling it to convert your object to the desired one.

#### Converting from parent class

Optionally, you can tell the conversion service to search for a converter configured for a parent class.
Supose you have a class B that inherits from class C, a converter from C to A, and you want to convert from B to A. This is possible by telling the converter to search for converters for parent classes:

```php
use Mcustiel\PhpSimpleConversion\ConversionService;
use Mcustiel\PhpSimpleConversion\ConverterBuilder;

$conversionService = new ConversionService();
// ...
$converter = ConverterBuilder::get()
    ->from(C::class)
    ->to(A::class)
    ->withImplementation(CtoAConverter::class);
$conversionService->registerConverter($converter);
$b = new B();
$a = $conversionService->convert($b, A::class, ConversionService::ALLOW_PARENTS);
```

Notes
-----

Currently you can only set 'string', 'array' or a full class name as 'from' and 'to' parameters in the converter builder.
