# PHP (Laravel) Geocode by address library
[![Build Status](https://travis-ci.org/madeITBelgium/geocode-by-address.svg?branch=master)](https://travis-ci.org/madeITBelgium/geocode-by-address)
[![Coverage Status](https://coveralls.io/repos/github/madeITBelgium/geocode-by-address/badge.svg?branch=master)](https://coveralls.io/github/madeITBelgium/geocode-by-address?branch=master)
[![Latest Stable Version](https://poser.pugx.org/madeITBelgium/geocode-by-address/v/stable.svg)](https://packagist.org/packages/madeITBelgium/geocode-by-address)
[![Latest Unstable Version](https://poser.pugx.org/madeITBelgium/geocode-by-address/v/unstable.svg)](https://packagist.org/packages/madeITBelgium/geocode-by-address)
[![Total Downloads](https://poser.pugx.org/madeITBelgium/geocode-by-address/d/total.svg)](https://packagist.org/packages/madeITBelgium/geocode-by-address)
[![License](https://poser.pugx.org/madeITBelgium/geocode-by-address/license.svg)](https://packagist.org/packages/madeITBelgium/geocode-by-address)

With this PHP (Laravel) package you can lookup GEO data by a given address.

# Installation

Require this package in your `composer.json` and update composer.

```php
composer require madeitbelgium/geocode-by-address
```
```php
"madeitbelgium/geocode-by-address": "^1.0"
```

After updating composer, add the ServiceProvider to the providers array in `config/app.php`

```php
MadeITBelgium\Geocode\ServiceProvider\Geocode::class,
```

You can use the facade for shorter code. Add this to your aliases:

```php
'Geocode' => MadeITBelgium\Geocode\Facade\Geocode::class,
```

Publish the configuration

```php
'Geocode' => MadeITBelgium\Geocode\Facade\Geocode::class,
```

# Documentation
## Usage
```php
use MadeITBelgium\Geocode\Geocode;

$geocode = new Geocode($type = 'geocode.xyz', $apikey = null, $client = null);
$geodata = $geocode->lookup('Nieuwstraat, Brussel, Belgium');
if($geodata !== false) {
    $latitude = $geodata[0];
    $longitude = $geodata[1];
}
```

In laravel you can use the Facade
```php
use MadeITBelgium\Geocode\Facade\Geocode;
$geodata = Geocode::lookup('Nieuwstraat, Brussel, Belgium');
if($geodata !== false) {
    $latitude = $geodata[0];
    $longitude = $geodata[1];
}
```

## Supported types
Currently supported GEO data providers:
Google: 'google'
Geocode.xyz: 'geocode.xyz'

The complete documentation can be found at: [http://www.madeit.be/](http://www.madeit.be/)

# Support

Support github or mail: tjebbe.lievens@madeit.be

# Contributing

Please try to follow the psr-2 coding style guide. http://www.php-fig.org/psr/psr-2/
# License

This package is licensed under LGPL. You are free to use it in personal and commercial projects. The code can be forked and modified, but the original copyright author should always be included!
