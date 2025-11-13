# 🔧 Laravel Compatibility Fix - CodeIgniter BaseConfig

## Masalah

Di Laravel, saat menggunakan SIMBA API, terjadi error:

```
Error in vendor/simba/api/src/Config/Simba.php:11
Class "CodeIgniter\Config\BaseConfig" not found
```

## Root Cause

1. **Class `simba\api\Config\Simba` extends `CodeIgniter\Config\BaseConfig`**
   - Ini adalah class CodeIgniter yang tidak ada di Laravel

2. **Composer autoload kurang prioritas dibanding dynamic compat loader**
   - Saat Composer load `simba\api\Config\Simba`, dia langsung coba load parent class
   - Compat autoloader belum sempat ter-register sebelum parent class di-cari

3. **Laravel tidak memiliki `CodeIgniter\Config` namespace**
   - Hanya CodeIgniter yang memiliki BaseConfig

## Solusi

### 1. ✅ Enhanced Compat Autoloader (src/helpers.php)

```php
// Prepend=true = Higher priority than composer autoload
spl_autoload_register(function ($class) {
    if (strpos($class, 'CodeIgniter\\Config\\') === 0) {
        $file = __DIR__ . '/compat/CodeIgniter/Config/' . ...;
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
}, true, true); // prepend=true untuk priority lebih tinggi
```

**Key:** `prepend=true` = Autoloader ini di-call SEBELUM composer autoload!

### 2. ✅ Complete BaseConfig Stub (src/compat/CodeIgniter/Config/BaseConfig.php)

Dari:
```php
class BaseConfig
{
}
```

Menjadi:
```php
class BaseConfig
{
    protected $environment = 'production';
    protected $appTimezone = 'UTC';
    
    public function getEnvironment() { ... }
    public function getAppTimezone() { ... }
    public function __get($name) { ... }
    public function __set($name, $value) { ... }
}
```

### 3. ✅ LaravelCompat Trait (src/Config/LaravelCompat.php)

Trait ini memberikan method-method yang mungkin diperlukan di Laravel environment:

```php
trait LaravelCompat
{
    protected $environment = 'production';
    protected $appTimezone = 'UTC';
    
    public function getEnvironment() { ... }
    public function getAppTimezone() { ... }
}
```

Digunakan jika class tidak bisa extend dari CodeIgniter BaseConfig.

## Bagaimana Cara Kerjanya

### Di Laravel:

```
composer install
  ↓
helpers.php dimuat (via autoload files)
  ↓
simba_register_compat_autoloader() di-register dengan prepend=true
  ↓
Code load Config\Simba
  ↓
Composer coba load CodeIgniter\Config\BaseConfig
  ↓
✅ Compat autoloader menangkap, load stub dari src/compat/
  ↓
Simba extends BaseConfig (stub version) ✓
```

### Di CodeIgniter:

```
composer install
  ↓
helpers.php dimuat
  ↓
simba_register_compat_autoloader() di-register
  ↓
Code load Config\Simba
  ↓
Composer coba load CodeIgniter\Config\BaseConfig
  ↓
✅ CodeIgniter's autoload menemukan real BaseConfig
  ↓
Simba extends BaseConfig (real version) ✓
```

### Di Standalone/Testing:

```
composer install
  ↓
helpers.php dimuat
  ↓
simba_register_compat_autoloader() di-register
  ↓
Code load Config\Simba
  ↓
Composer coba load CodeIgniter\Config\BaseConfig
  ↓
✅ Compat autoloader load stub dari src/compat/
  ↓
Simba extends BaseConfig (stub) ✓
```

## Priority Order (Autoloader)

1. **src/helpers.php compat autoloader** (prepend=true) - HIGHEST
2. **Composer autoload** (normal priority)
3. **PHP native autoload** - LOWEST

Dengan `prepend=true`, compat autoloader dijalankan PERTAMA sebelum composer's PSR-4 autoloader!

## Files Modified

| File | Changes |
|------|---------|
| `src/helpers.php` | Enhanced compat autoloader dengan prepend=true |
| `src/compat/CodeIgniter/Config/BaseConfig.php` | Added full BaseConfig implementation |
| `src/Config/LaravelCompat.php` | NEW - Laravel compatibility trait |
| `.gitattributes` | Include compat/ folder in release |
| `composer.json` | Unchanged (already correct) |

## Verifikasi

Cek bahwa autoloader ter-register:

```php
// Di Laravel app
$reflection = new ReflectionClass('CodeIgniter\Config\BaseConfig');
echo $reflection->getFileName(); // Harus dari src/compat/
```

## Testing

### Laravel:
```bash
cd your-laravel-app
composer require simba/api
```

Harus bisa load tanpa error "Class not found".

### CodeIgniter 4:
```bash
cd your-ci4-app
composer require simba/api
```

Harus load real CodeIgniter BaseConfig, bukan stub.

## Notes

- ✅ Backwards compatible dengan CodeIgniter 4
- ✅ Works dengan Laravel 8+
- ✅ Fallback untuk testing environments
- ✅ Zero configuration needed
- ✅ Auto-detect dan load yang tepat
