<?php

declare(strict_types=1);

namespace Scabarcas\LaravelConfigExplorer\Http\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureExplorerIsEnabled
{
    public function __construct(
        private readonly Repository $config,
        private readonly Application $app,
    ) {
    }

    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isEnabled()) {
            abort(404);
        }

        return $next($request);
    }

    private function isEnabled(): bool
    {
        $configured = $this->config->get('config-explorer.enabled');

        if ($configured === null) {
            return (bool) $this->config->get('app.debug') && !$this->app->environment('production');
        }

        return (bool) $configured;
    }
}
