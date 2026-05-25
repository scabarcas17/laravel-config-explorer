# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-05-25

Initial release.

### Added

- Searchable, sortable browser for the merged Laravel `config()` tree.
- Server-rendered Blade view with Alpine.js client-side filtering, group selector and dark-mode toggle.
- `ConfigFlattener` support class flattens nested config to dot-notation entries with type detection (`bool`, `null`, `int`, `float`, `string`, `list`, `array`, object class name).
- `EnsureExplorerIsEnabled` middleware: auto-detects from `APP_DEBUG` outside of production, or honors explicit `CONFIG_EXPLORER_ENABLED` env. Returns 404 when disabled.
- Pattern-based credential redaction with sensible defaults (`*password*`, `*secret*`, `*token*`, `*api_key*`, `*api-key*`, `*private_key*`, `app.key`, `services.*.secret`, `database.connections.*.password`, `mail.mailers.*.password`).
- `noindex, nofollow` meta tag and explicit dev-only positioning on the rendered page.
- Auto-discovered service provider; publishable config (`config-explorer-config` tag) and views (`config-explorer-views` tag).
- GitHub Actions CI: lint job (`composer validate --strict`, PHPStan max, Pint), tests matrix (PHP 8.3/8.4 × Laravel 12/13), coverage job uploading to Codecov.

[Unreleased]: https://github.com/scabarcas17/laravel-config-explorer/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/scabarcas17/laravel-config-explorer/releases/tag/v0.1.0
