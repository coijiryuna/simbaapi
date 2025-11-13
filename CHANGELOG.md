# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.0] - 2025-11-13

### Added
- **Full Laravel Support** — Package now works seamlessly with Laravel 8+ applications
- **Dependency Injection** — Client now accepts optional injected HTTP client for testability
- **Laravel Service Provider** — Auto-discoverable ServiceProvider with container bindings
- **Publishable Config** — Laravel apps can publish `config/simba.php` via `php artisan vendor:publish`
- **SmartClient Fallbacks** — Client uses: injected client → Laravel Http facade → CodeIgniter Services
- **Enhanced SimbaManager** — Resolves libraries from container when in Laravel, falls back to static methods
- **Updated Documentation** — README with Laravel quick-start guide and setup instructions

### Changed
- **Client Constructor** — Now accepts optional `$httpClient` parameter (backward compatible)
- **Library Constructors** — All libraries (Muzakki, Mustahik, Pengumpulan, Upz) accept optional `$httpClient`
- **Composer Autoload** — Added Laravel autodiscovery entries and helper shims
- **README** — Added Laravel section with installation and usage examples

### Improved
- Better testability with injectable dependencies
- Framework-agnostic HTTP client handling
- More flexible service container integration

### Fixed
- CI helper dependencies reduced via shims
- Type safety improvements across libraries

### Tested
- ✅ PHPUnit: All tests passing
- ✅ Syntax: All files validated with `php -l`
- ✅ Backward Compatibility: Existing CodeIgniter code works unchanged
- ✅ Laravel Integration: Verified with container resolution and facade

## [2.0.0] - 2024-02-03

### Initial Release
- SIMBA API library for CodeIgniter 4
- Core libraries: Muzakki, Mustahik, Pengumpulan, Penyaluran, Upz
- Response formatter and validation traits
- Database migrations and seeders
- Comprehensive documentation

---

## Usage

### CodeIgniter 4
```php
use simba\api\Libraries\Muzakki;

$muzakki = new Muzakki();
$response = $muzakki->registerDariLokal(1, $data);
```

### Laravel (New in v2.1)
```php
// Via container
$muzakki = app('simba')->muzakki();

// Via Facade
$muzakki = \Simba::muzakki();
```

## Migration from v2.0 to v2.1

No breaking changes! All existing code continues to work. To leverage new Laravel features:

1. Install and publish config:
   ```bash
   composer require simba/api:^2.1
   php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider"
   ```

2. Update `.env`:
   ```env
   SIMBA_BASE_URL=https://demo-simba.baznas.or.id/
   SIMBA_ORG_CODE=your_org_code
   SIMBA_API_KEY=your_api_key
   SIMBA_ADMIN_EMAIL=admin@example.com
   ```

3. Start using in Laravel controllers:
   ```php
   $muzakki = app('simba')->muzakki();
   ```
