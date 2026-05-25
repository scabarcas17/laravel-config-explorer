<?php

declare(strict_types=1);

it('renders the explorer page when enabled', function (): void {
    config()->set('config-explorer.enabled', true);
    config()->set('app.test_marker', 'config-explorer-marker-value');

    $this->get('/config-explorer')
        ->assertOk()
        ->assertSee('Config Explorer')
        ->assertSee('app.test_marker')
        ->assertSee('config-explorer-marker-value');
});

it('returns 404 when explicitly disabled', function (): void {
    config()->set('config-explorer.enabled', false);

    $this->get('/config-explorer')->assertNotFound();
});

it('redacts values matching default patterns', function (): void {
    config()->set('config-explorer.enabled', true);
    config()->set('services.stripe.secret', 'sk_live_xxx_should_not_appear');

    $response = $this->get('/config-explorer')->assertOk();

    expect($response->getContent())
        ->not->toContain('sk_live_xxx_should_not_appear')
        ->and($response->getContent())->toContain('[REDACTED]');
});

it('emits a noindex meta tag', function (): void {
    config()->set('config-explorer.enabled', true);

    $this->get('/config-explorer')
        ->assertOk()
        ->assertSee('noindex, nofollow', false);
});

it('uses the configured route prefix and name', function (): void {
    config()->set('config-explorer.enabled', true);

    expect(route('config-explorer.show'))->toEndWith('/config-explorer');
});
