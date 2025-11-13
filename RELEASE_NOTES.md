# 🚀 SIMBA API v2.1.0 Released!

**Date**: November 13, 2025

## ✨ What's New?

### 🎯 Full Laravel Support
The SIMBA API package now works seamlessly with Laravel 8+ applications!

**Features:**
- ✅ Laravel ServiceProvider with container bindings and autodiscovery
- ✅ Publishable configuration file (`config/simba.php`)
- ✅ Dependency injection support across all libraries
- ✅ Laravel Http client integration with smart fallbacks
- ✅ Facade for convenient library access (`\Simba::muzakki()`)
- ✅ 100% backward compatible with CodeIgniter 4

### 🔧 Dependency Injection
Client now accepts optional injected HTTP client:
```php
// Laravel: Automatic injection via container
$muzakki = app('simba')->muzakki();

// CodeIgniter: Works unchanged
$muzakki = new Muzakki();
```

### 📦 Installation

**For Laravel:**
```bash
composer require simba/api:^2.1
php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider"
```

**For CodeIgniter 4:**
```bash
composer require simba/api:^2.1
php spark simba:install
```

## 📊 Release Stats

| Metric | Status |
|--------|--------|
| Tests | ✅ Passing (1/1) |
| Syntax | ✅ Valid |
| Breaking Changes | ❌ None |
| Backward Compatibility | ✅ 100% |
| Laravel Support | ✅ New |

## 🔗 Resources

- **GitHub Release**: https://github.com/coijiryuna/simbaapi/releases/tag/v2.1.0
- **Changelog**: See [CHANGELOG.md](CHANGELOG.md)
- **Documentation**: See [DOCUMENTATION.md](DOCUMENTATION.md)
- **Installation Guide**: See [INSTALLATION.md](INSTALLATION.md)

## 🎁 What's Included

```
📦 v2.1.0
├── 🎯 Laravel Integration
│   ├── ServiceProvider with autodiscovery
│   ├── Container bindings & Facade
│   └── Publishable config
├── 🔧 Dependency Injection
│   ├── Injected HTTP client support
│   ├── Testable mocks
│   └── Smart fallbacks
├── 📚 Libraries Updated
│   ├── Muzakki (Donor management)
│   ├── Mustahik (Recipient management)
│   ├── Pengumpulan (Inbound transactions)
│   ├── Penyaluran (Outbound transactions)
│   └── Upz (UPZ management)
└── 📖 Documentation
    ├── README with Laravel quick-start
    ├── CHANGELOG with full details
    └── INSTALLATION guide for both frameworks
```

## 🚦 Next Steps

1. **Review the Changes**: https://github.com/coijiryuna/simbaapi/compare/v2.0.0...v2.1.0
2. **Read the Changelog**: CHANGELOG.md for detailed changes
3. **Update Your App**: Follow installation instructions above
4. **Report Issues**: GitHub Issues page

## ❓ FAQ

**Q: Will this break my existing CodeIgniter app?**
A: No! This release is 100% backward compatible. Your existing code continues to work unchanged.

**Q: Can I use this in Laravel now?**
A: Yes! Full Laravel support is here. See installation instructions above.

**Q: Do I need to update my code?**
A: No, but we recommend it for better testability and framework integration.

---

**Released by**: @coijiryuna  
**Repository**: https://github.com/coijiryuna/simbaapi  
**License**: MIT
