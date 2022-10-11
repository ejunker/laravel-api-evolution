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

The `api-evolution:install` command will create the `config/api-evolution.php` config file.

You will need to add the middleware to your `api` middleware group in `app/Http/Kernel.php` or to the group/route that you want.

```php
'api' => [
    // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
    \Ejunker\LaravelApiEvolution\ApiEvolutionMiddleware::class,
],
```

## Usage

You can create an API migration using the command:

```php
php artisan make:api-migration FirstLastName
```

This will create the file `app/Http/Migrations/FirstLastName.php` that you can edit to make the necessary changes to the
request and/or response. Then you need to add it to the `config/api-evolution.php` file so that it runs when an old version is requested.
The migrations listed for a version are the migrations needed to make it match the previous version.

## API Migrations

Each API migration file has several properties and methods that you can use:
- `routeNames` - an array of route names that this migration will run for. You can use wildcards such as `api.v1.users.*`
- `isApplicable()` - if `routeNames` does not give you enough flexibility, you can use this method to determine if the migration should run based on the `Request`
- `migrateRequest()` - allows you to modify the `Request` so that it is compatible with the newer version
- `migrateResponse()` - allows you to modify the `Response` so that it is the older version that was requested

In addition to `routeNames` and `isApplicable()`, you can also specify the route names for a migration in the config file.
```php
'versions' => [
    '2022-10-10' => [
        new \App\Http\Migrations\FirstLastName(['api.v1.users.show']),
    ],

    '2022-10-05' => [
        // first version
    ],
],
```

## Binds

While API Migrations allow you to modify the `Request` and `Response`, sometimes it may be easier to use an entirely different
FormRequest, JsonResource, or response Transformer. In those cases you can use a `Bind` to bind a different version into the container.

In the `config/api-evolution.php`
```php
'versions' => [
    '2022-10-10' => [
        \App\Http\Migrations\FirstLastName::class
        new \Ejunker\LaravelApiEvolution\Bind(
            \App\Transformers\UserTransformer::class,
            \App\Transformers\UserTransformer_20221005::class,
        ),
    ],

    '2022-10-05' => [
        // first version
    ],
],
```

In this example, if the user requested the `2022-10-05` version it would apply the `Bind` which would bind the old
version of the file into the container so that it would be used. If the user requested the latest version `2022-10-10`
then the `Bind` would not run and the latest version of the file would be used.

## Determine if a Migration Is Active

If you need to modify things other than Request/Response such as modifying a SQL query based on the version then you can
use `ApiEvolution::isActive()`.
For example, to know if the `FirstLastName` migration is active: `ApiEvolution::isActive(FirstLastName::class)`

## Response Headers

Several headers are added to the response:
- `API-Version` - the version requested or the latest version if no specific version is requested
- `API-Version-Latest` - if the version requested is not the latest version then this header will be added
- `Deprecated` - if there are migrations or Binds that run for this endpoint then this header will be added to indicate that there is a newer version of this endpoint

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

- [tomschlick/request-migrations](https://github.com/tomschlick/request-migrations)
- [lukepolo/laravel-api-migrations](https://github.com/lukepolo/laravel-api-migrations)
- [ds-labs/laravel-redaktor](https://github.com/ds-labs/laravel-redaktor)
- [reindert-vetter/api-version-control](https://github.com/reindert-vetter/api-version-control)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
