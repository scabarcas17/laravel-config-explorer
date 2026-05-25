# Laravel Config Explorer

[![CI](https://github.com/scabarcas17/laravel-config-explorer/actions/workflows/ci.yml/badge.svg)](https://github.com/scabarcas17/laravel-config-explorer/actions/workflows/ci.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/scabarcas/laravel-config-explorer.svg)](https://packagist.org/packages/scabarcas/laravel-config-explorer)
[![Total Downloads](https://img.shields.io/packagist/dt/scabarcas/laravel-config-explorer.svg)](https://packagist.org/packages/scabarcas/laravel-config-explorer)
[![PHP Version](https://img.shields.io/packagist/php-v/scabarcas/laravel-config-explorer.svg)](https://packagist.org/packages/scabarcas/laravel-config-explorer)
[![License](https://img.shields.io/packagist/l/scabarcas/laravel-config-explorer.svg?v=0.1.0)](https://github.com/scabarcas17/laravel-config-explorer/blob/main/LICENSE)

A beautiful, searchable browser for your Laravel runtime configuration — the `phpinfo()` of `config()`.

`php artisan config:show <key>` is great when you know what you're looking for. Config Explorer is for the moments you don't: scan every merged config entry across your app and its packages, filter by group, and grep through values instantly. Built for local debugging.

## Installation

```bash
composer require scabarcas/laravel-config-explorer --dev
```

The service provider is auto-discovered. Visit [http://your-app.test/config-explorer](http://your-app.test/config-explorer) in your local environment.

## Configuration

Publish the config file to customize:

```bash
php artisan vendor:publish --tag=config-explorer-config
```

```php
return [
    // null = auto-detect (debug + non-production); true/false to force.
    'enabled' => env('CONFIG_EXPLORER_ENABLED'),

    'route' => [
        'prefix'     => 'config-explorer',
        'middleware' => ['web'],
        'name'       => 'config-explorer.show',
    ],

    'redact_patterns' => [
        '*password*',
        '*secret*',
        '*token*',
        // ...add patterns specific to your app
    ],
];
```

### Enabling

By default the explorer route is mounted but the request returns 404 unless:

- `CONFIG_EXPLORER_ENABLED=true`, **or**
- `APP_DEBUG=true` and `APP_ENV` is not `production`.

To expose the explorer behind authentication in a non-local environment (e.g. staging), enable it explicitly and add middleware:

```php
'enabled'    => true,
'middleware' => ['web', 'auth', 'can:viewConfigExplorer'],
```

## Security

Configuration data routinely contains credentials. Config Explorer redacts keys matching the `redact_patterns` list before rendering. The defaults cover common cases (`*password*`, `*secret*`, `*token*`, `*api_key*`, `app.key`, `services.*.secret`, `database.connections.*.password`, `mail.mailers.*.password`) but **review the list for your app before enabling this anywhere other than your local machine**.

The route also emits `<meta name="robots" content="noindex, nofollow">` and ships with auto-detection that returns 404 in production. Treat any deliberate production enablement as a deliberate decision that requires authentication.

## Why?

Inspired by [stechstudio/phpinfo](https://packagist.org/packages/stechstudio/phpinfo), which solves the same problem for PHP's `phpinfo()`. Laravel ships `php artisan config:show` for known keys; this package covers the discovery side.

## Testing

```bash
composer install
composer test
composer analyse
composer format
```

## Author

**Sebastian Cabarcas Berrio** &middot; <sebastianberrio45@hotmail.com> &middot; [@scabarcas17](https://github.com/scabarcas17)

## License

MIT &copy; Sebastian Cabarcas Berrio
