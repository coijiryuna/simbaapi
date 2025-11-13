# 🔧 Compatibility Layer (compat/)

**Purpose**: Provide compatibility stubs for cross-framework support

## 📝 Overview

This folder contains minimal stub implementations of framework-specific classes to enable the SIMBA API library to work across multiple PHP frameworks and environments.

## 📂 Structure

```
compat/
├── CodeIgniter/
│   └── Config/
│       └── BaseConfig.php       # CodeIgniter BaseConfig stub
└── Config/
    └── Services.php             # CodeIgniter Services stub
```

## 🎯 Why These Exist

### 1. **BaseConfig.php** (`CodeIgniter\Config\BaseConfig`)
- **Purpose**: Stub for CodeIgniter's base configuration class
- **Why needed**: Configuration class in Simba might extend this in CodeIgniter environment
- **Usage**: Autoloaded as fallback if CodeIgniter is not installed
- **Framework**: Used in CodeIgniter projects, harmless stub in others

### 2. **Services.php** (`Config\Services`)
- **Purpose**: Stub for CodeIgniter's service locator
- **Why needed**: Provides `curlrequest()` method as HTTP client fallback
- **Usage**: Used in `Client.php` when Laravel Http Facade is not available
- **Framework**: CodeIgniter 4 uses `Services::curlrequest()` for HTTP requests

## 🔄 How It Works

### HTTP Client Detection Priority (in `Client.php`):

```
1. Injected HTTP client (Laravel Http Factory or similar)
   ↓ (not available)
2. Laravel Http Facade (\Illuminate\Support\Facades\Http)
   ↓ (not available)
3. CodeIgniter Services::curlrequest() (from Config\Services)
   ↓ (not available)
4. Native PHP cURL (fallback)
   ↓ (not available)
5. Error with helpful message
```

## ✅ Composer Configuration

These stubs are auto-loaded via `composer.json`:

```json
"autoload": {
    "psr-4": {
        "CodeIgniter\\Config\\": "src/compat/CodeIgniter/Config/",
        "Config\\": "src/compat/Config/"
    }
}
```

This means:
- When `use Config\Services;` is called, it loads from `src/compat/Config/Services.php`
- When `use CodeIgniter\Config\BaseConfig;` is called, it loads from `src/compat/CodeIgniter/Config/BaseConfig.php`

## 🎓 Example Scenario

### In a Pure Laravel Application:
1. `Config\Services` class is loaded (from compat stub)
2. `Client.php` checks: `class_exists('Config\Services')` → true
3. Tries to use `Services::curlrequest()` → fails gracefully (try-catch)
4. Falls back to Laravel Http Facade ✅

### In a CodeIgniter Application:
1. `Config\Services` class is loaded (from compat stub or real CodeIgniter)
2. `Client.php` checks: `class_exists('Config\Services')` → true
3. Uses `Services::curlrequest()` successfully ✅

## ⚙️ Important Notes

- **DO NOT DELETE** - These files are essential for cross-framework compatibility
- **DO NOT MODIFY** - Unless updating framework compatibility requirements
- **MINIMAL CODE** - Intentionally simple, just enough to avoid class-not-found errors
- **VERSION-AGNOSTIC** - Works with both CodeIgniter 4 and no CodeIgniter

## 🔗 Related Files

- `src/Client.php` - Uses these stubs for HTTP client detection
- `composer.json` - Defines autoloading for these stubs
- `src/helpers.php` - Additional compatibility helpers

---

**Status**: ✅ Required  
**Last Updated**: November 2025  
**Importance**: High - Critical for framework detection and fallback mechanism
