<?php

declare(strict_types=1);

namespace Scabarcas\LaravelConfigExplorer\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Scabarcas\LaravelConfigExplorer\ConfigExplorerServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ConfigExplorerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $app['config']->set('config-explorer.enabled', true);
    }
}
