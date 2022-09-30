# Evolve your API while maintaining backwards compatibility.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ejunker/laravel-api-evolution.svg?style=flat-square)](https://packagist.org/packages/ejunker/laravel-api-evolution)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/ejunker/laravel-api-evolution/run-tests?label=tests)](https://github.com/ejunker/laravel-api-evolution/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/ejunker/laravel-api-evolution/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/ejunker/laravel-api-evolution/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ejunker/laravel-api-evolution.svg?style=flat-square)](https://packagist.org/packages/ejunker/laravel-api-evolution)

Laravel API Evolution is an API versioning library based on the idea of API evolution.
It provides a way to make changes to your API way while maintaining backwards compatibility.

Inspired by [Stripe's API versioning](https://stripe.com/blog/api-versioning) strategy.
Users specify the desired version in a header and the request and response data will be modified to match the requested version.

## Installation

You can install the package via composer:

```bash
composer require ejunker/laravel-api-evolution
```

You can run the installation command with:

```
php artisan api-evolution:install
```

## Usage

```php
$laravelApiEvolution = new Ejunker\LaravelApiEvolution();
echo $laravelApiEvolution->echoPhrase('Hello, Ejunker!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Eric Junker](https://github.com/ejunker)
- [All Contributors](../../contributors)

This package is based on the following:

- https://github.com/tomschlick/request-migrations
- https://github.com/lukepolo/laravel-api-migrations
- https://github.com/ds-labs/laravel-redaktor

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
