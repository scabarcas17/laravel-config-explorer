<?php

declare(strict_types=1);

namespace Scabarcas\LaravelConfigExplorer\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Scabarcas\LaravelConfigExplorer\Support\ConfigFlattener;

class ConfigExplorerController
{
    public function __construct(
        private readonly ConfigFlattener $flattener,
        private readonly Repository $config,
        private readonly Application $app,
        private readonly ViewFactory $views,
    ) {
    }

    public function __invoke(): View
    {
        /** @var array<int, string> $patterns */
        $patterns = $this->config->get('config-explorer.redact_patterns', []);

        /** @var array<string, mixed> $configuration */
        $configuration = $this->config->all();

        $entries = $this->flattener->flatten($configuration, $patterns);

        return $this->views->make('config-explorer::explorer', [
            'entries'        => $entries,
            'groups'         => $this->groupLabels($entries),
            'phpVersion'     => PHP_VERSION,
            'laravelVersion' => $this->app->version(),
            'environment'    => $this->app->environment(),
        ]);
    }

    /**
     * @param list<array{key: string, value: string, type: string, group: string, redacted: bool}> $entries
     *
     * @return list<string>
     */
    private function groupLabels(array $entries): array
    {
        $groups = [];

        foreach ($entries as $entry) {
            $groups[$entry['group']] = true;
        }

        $labels = array_keys($groups);
        sort($labels);

        return $labels;
    }
}
