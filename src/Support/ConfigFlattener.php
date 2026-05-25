<?php

declare(strict_types=1);

namespace Scabarcas\LaravelConfigExplorer\Support;

use Illuminate\Support\Str;

class ConfigFlattener
{
    /**
     * @param array<string, mixed> $config
     * @param array<int, string>   $redactPatterns
     *
     * @return list<array{key: string, value: string, type: string, group: string, redacted: bool}>
     */
    public function flatten(array $config, array $redactPatterns = []): array
    {
        $entries = [];

        foreach ($config as $group => $values) {
            $this->walk((string) $group, (string) $group, $values, $redactPatterns, $entries);
        }

        usort($entries, static fn (array $a, array $b): int => strcmp($a['key'], $b['key']));

        return $entries;
    }

    /**
     * @param array<int, string>                                                                   $redactPatterns
     * @param list<array{key: string, value: string, type: string, group: string, redacted: bool}> $entries
     */
    private function walk(string $key, string $group, mixed $value, array $redactPatterns, array &$entries): void
    {
        if (is_array($value) && $this->isAssociative($value)) {
            foreach ($value as $childKey => $childValue) {
                $this->walk($key . '.' . $childKey, $group, $childValue, $redactPatterns, $entries);
            }

            return;
        }

        $redacted = $this->matchesAnyPattern($key, $redactPatterns);

        $entries[] = [
            'key'      => $key,
            'value'    => $redacted ? '[REDACTED]' : $this->renderValue($value),
            'type'     => $this->renderType($value),
            'group'    => $group,
            'redacted' => $redacted,
        ];
    }

    /**
     * @param array<int|string, mixed> $value
     */
    private function isAssociative(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        return !array_is_list($value);
    }

    /**
     * @param array<int, string> $patterns
     */
    private function matchesAnyPattern(string $key, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $key)) {
                return true;
            }
        }

        return false;
    }

    private function renderValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            $encoded = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            return $encoded !== false ? $encoded : '[unserializable array]';
        }

        if (is_object($value)) {
            return $value::class;
        }

        return '[unprintable]';
    }

    private function renderType(mixed $value): string
    {
        if (is_array($value)) {
            return array_is_list($value) ? 'list' : 'array';
        }

        return get_debug_type($value);
    }
}
