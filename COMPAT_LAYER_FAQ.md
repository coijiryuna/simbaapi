# ❓ FAQ: src/compat/ Folder

## Q: Apakah Services.php dan BaseConfig.php perlu dihapus?

### ✅ **JAWABAN: TIDAK. Kedua file HARUS dipertahankan.**

---

## 📊 Visualisasi: Mengapa Compat Layer Penting

```
┌─────────────────────────────────────────────────────────────┐
│           Application Menggunakan SIMBA API                 │
└────┬──────────────────────────────────────────────────────┬─┘
     │                                                      │
     ▼                                                      ▼
┌──────────────┐                                 ┌──────────────┐
│   Laravel    │                                 │ CodeIgniter  │
│   8+         │                                 │   4          │
└──────┬───────┘                                 └──────┬───────┘
       │                                                │
       │ composer.json autoload                        │
       │ Config\ → src/compat/Config/                  │
       │ CodeIgniter\Config\ →                         │ CodeIgniter
       │ src/compat/CodeIgniter/Config/                │ Services
       │                                                │
       ▼                                                ▼
   ┌───────────────────────────────────────────────────────────┐
   │         Client.php HTTP Client Detection                  │
   │                                                             │
   │  1. Check Laravel Http Facade ✅                          │
   │     - Found in Laravel → Use it                           │
   │     - Not found → Continue                                │
   │                                                             │
   │  2. Check Config\Services (from compat) ✅                │
   │     - Try Services::curlrequest()                         │
   │     - Success → Use it (CodeIgniter env)                  │
   │     - Fail → Continue (non-CI env)                        │
   │                                                             │
   │  3. Check cURL Extension ✅                               │
   │     - Use native cURL → Success                           │
   │     - Not available → Error message                       │
   └───────────────────────────────────────────────────────────┘
```

---

## 🔗 Dependency Chain

```
composer.json
    ↓
    ├─ "Config\\": "src/compat/Config/"
    │   ↓
    │   └─ Services.php (stub)
    │       ↓
    │       ✅ Used in Client.php line 5:
    │          use Config\Services;
    │
    └─ "CodeIgniter\\Config\\": "src/compat/CodeIgniter/Config/"
        ↓
        └─ BaseConfig.php (stub)
            ↓
            ✅ Used for config compatibility
```

---

## 📌 Real-World Scenario

### Scenario A: Pure Laravel (No CodeIgniter)

```php
// Client.php constructor runs:
if (class_exists('\Illuminate\Support\Facades\Http')) {
    // ✅ Found! Use Laravel Http Facade
    // compat files not used but not harmful
}
```

### Scenario B: Pure CodeIgniter

```php
// Client.php constructor runs:
if (class_exists('Config\Services')) {
    // ✅ Found! Use CodeIgniter Services::curlrequest()
    // Uses real CodeIgniter Config\Services, not compat stub
}
```

### Scenario C: Testing / Dev Environment (No Framework)

```php
// Client.php constructor runs:
if (class_exists('Config\Services')) {
    // ✅ Found! Uses compat stub from src/compat/Config/
    // try-catch handles graceful fallback
    try {
        Services::curlrequest(...);
    } catch {
        // Fallback to cURL or error
    }
}
```

---

## 🎯 File Contents Explanation

### BaseConfig.php
```php
<?php
namespace CodeIgniter\Config;

/**
 * Minimal stub of CodeIgniter\Config\BaseConfig 
 * to allow package to run in non-CI environments during tests.
 */
class BaseConfig
{
    // Empty - just a class definition placeholder
}
```
**Purpose**: Prevent "class not found" errors when CodeIgniter is not installed

### Services.php
```php
<?php
namespace Config;

class Services
{
    public static function curlrequest($options = [])
    {
        return new class {
            public function request($method, $url, $options) {
                // Minimal implementation
                // In real CodeIgniter: actual HTTP handling
                // In other environments: gracefully fails in try-catch
            }
        };
    }
}
```
**Purpose**: Provide fallback HTTP client method for non-CodeIgniter environments

---

## ❌ Apa yang Terjadi Jika Dihapus?

### Error Chain:
```
1. composer.json autoload akan gagal parse
   "Config\\": "src/compat/Config/" ← Path tidak ada!

2. PHP fatal error saat load:
   Class 'Config\Services' not found
   
3. Client.php akan crash:
   use Config\Services; ← undefined class

4. Application breaks:
   All library usage will fail
```

---

## ✅ Kesimpulan

| Aspek | Status |
|-------|--------|
| **Dihapus?** | ❌ **NO** |
| **Dimodifikasi?** | ❌ **NO** |
| **Diperlukan?** | ✅ **YES** |
| **Penting?** | ✅ **CRITICAL** |

**Folder `src/compat/` adalah bagian integral dari arsitektur cross-framework.**

Jangan dihapus! 🚀

---

**Last Updated**: November 13, 2025  
**Version**: v2.1.0
