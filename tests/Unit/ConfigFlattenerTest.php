<?php

declare(strict_types=1);

use Scabarcas\LaravelConfigExplorer\Support\ConfigFlattener;

it('flattens nested config to sorted dot-notation entries', function (): void {
    $flattener = new ConfigFlattener();

    $entries = $flattener->flatten([
        'app' => [
            'name'  => 'Test',
            'debug' => true,
        ],
        'database' => [
            'default'     => 'mysql',
            'connections' => [
                'mysql' => [
                    'host' => '127.0.0.1',
                    'port' => 3306,
                ],
            ],
        ],
    ]);

    $keys = array_column($entries, 'key');

    expect($entries)->toHaveCount(5)
        ->and($keys)->toEqual([
            'app.debug',
            'app.name',
            'database.connections.mysql.host',
            'database.connections.mysql.port',
            'database.default',
        ]);
});

it('assigns the top-level config file as the group', function (): void {
    $flattener = new ConfigFlattener();

    $entries = $flattener->flatten([
        'app'     => ['name' => 'Test'],
        'session' => ['driver' => 'file'],
    ]);

    $byKey = collect($entries)->keyBy('key');

    expect($byKey['app.name']['group'])->toBe('app')
        ->and($byKey['session.driver']['group'])->toBe('session');
});

it('renders scalar types with normalized strings', function (): void {
    $flattener = new ConfigFlattener();

    $entries = $flattener->flatten([
        'a' => [
            'bool'  => true,
            'null'  => null,
            'int'   => 42,
            'float' => 3.14,
            'str'   => 'hello',
        ],
    ]);

    $byKey = collect($entries)->keyBy('key');

    expect($byKey['a.bool']['value'])->toBe('true')
        ->and($byKey['a.bool']['type'])->toBe('bool')
        ->and($byKey['a.null']['value'])->toBe('null')
        ->and($byKey['a.null']['type'])->toBe('null')
        ->and($byKey['a.int']['value'])->toBe('42')
        ->and($byKey['a.int']['type'])->toBe('int')
        ->and($byKey['a.float']['value'])->toBe('3.14')
        ->and($byKey['a.float']['type'])->toBe('float')
        ->and($byKey['a.str']['value'])->toBe('hello')
        ->and($byKey['a.str']['type'])->toBe('string');
});

it('treats list arrays as a single JSON-encoded entry', function (): void {
    $flattener = new ConfigFlattener();

    $entries = $flattener->flatten([
        'logging' => ['channels' => ['stack', 'single']],
    ]);

    expect($entries)->toHaveCount(1)
        ->and($entries[0]['key'])->toBe('logging.channels')
        ->and($entries[0]['type'])->toBe('list')
        ->and($entries[0]['value'])->toBe('["stack","single"]');
});

it('redacts values whose key matches any pattern', function (): void {
    $flattener = new ConfigFlattener();

    $entries = $flattener->flatten(
        [
            'services' => [
                'stripe' => [
                    'secret' => 'sk_test_xxx',
                    'key'    => 'pk_test_xxx',
                ],
            ],
        ],
        ['*secret*'],
    );

    $byKey = collect($entries)->keyBy('key');

    expect($byKey['services.stripe.secret']['value'])->toBe('[REDACTED]')
        ->and($byKey['services.stripe.secret']['redacted'])->toBeTrue()
        ->and($byKey['services.stripe.key']['value'])->toBe('pk_test_xxx')
        ->and($byKey['services.stripe.key']['redacted'])->toBeFalse();
});

it('returns the class name for objects', function (): void {
    $flattener = new ConfigFlattener();

    $entries = $flattener->flatten([
        'a' => ['obj' => new stdClass()],
    ]);

    expect($entries[0]['value'])->toBe('stdClass')
        ->and($entries[0]['type'])->toBe('stdClass');
});
