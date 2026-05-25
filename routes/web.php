<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Scabarcas\LaravelConfigExplorer\Http\Controllers\ConfigExplorerController;
use Scabarcas\LaravelConfigExplorer\Http\Middleware\EnsureExplorerIsEnabled;

/** @var array<int, string> $middleware */
$middleware = config('config-explorer.route.middleware', ['web']);

/** @var string $prefix */
$prefix = config('config-explorer.route.prefix', 'config-explorer');

/** @var string $name */
$name = config('config-explorer.route.name', 'config-explorer.show');

Route::middleware([...$middleware, EnsureExplorerIsEnabled::class])
    ->prefix($prefix)
    ->group(static function () use ($name): void {
        Route::get('/', ConfigExplorerController::class)->name($name);
    });
