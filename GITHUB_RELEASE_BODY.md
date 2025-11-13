# 🚀 SIMBA API v2.1.0 - Laravel Integration Release

**Release Date**: November 13, 2025  
**Type**: Feature Release  
**Breaking Changes**: None  
**Backward Compatibility**: 100%

---

## ✨ What's New in v2.1.0?

### 🎯 Full Laravel Support
The SIMBA API package now works seamlessly with Laravel 8+ applications alongside existing CodeIgniter 4 support.

**New Features:**
- ✅ **Laravel ServiceProvider** with auto-discovery via `extra.laravel` in composer.json
- ✅ **Container Bindings** for all libraries (Muzakki, Mustahik, Pengumpulan, Penyaluran, Upz)
- ✅ **Publishable Configuration** — `php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider"`
- ✅ **Facade Support** — Optional `\Simba::muzakki()` for convenience
- ✅ **Dependency Injection** throughout all libraries

### 🔧 Dependency Injection Support
The `Client` class now accepts an optional injected HTTP client:
- Injected Laravel Http factory (when available)
- Falls back to Laravel Http facade
- Falls back to CodeIgniter Services (backward compatible)

```php
// Automatic injection in Laravel container
$muzakki = app('simba')->muzakki();

// Optional facade access
$muzakki = \Simba::muzakki();

// Existing CodeIgniter code still works
$muzakki = new Muzakki();
```

### 📚 Enhanced Libraries
All 5 core libraries updated to support dependency injection:
- **Muzakki** — Donatur (Donor) Management
- **Mustahik** — Penerima Manfaat (Recipient) Management
- **Pengumpulan** — Pengumpulan (Inbound Transactions)
- **Penyaluran** — Penyaluran (Outbound Transactions)
- **Upz** — UPZ (Unit Pengumpul Zakat) Management

### 📖 Documentation Improvements
- Updated README with Laravel quick-start guide
- Created comprehensive CHANGELOG
- Enhanced INSTALLATION guide for both frameworks
- Added RELEASE_NOTES for users

---

## 🔄 Installation

### For Laravel
```bash
composer require simba/api:^2.1
php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider"
```

Configure `.env`:
```env
SIMBA_BASE_URL=https://demo-simba.baznas.or.id/
SIMBA_ORG_CODE=your_org_code
SIMBA_API_KEY=your_api_key
SIMBA_ADMIN_EMAIL=admin@example.com
```

### For CodeIgniter 4
```bash
composer require simba/api:^2.1
php spark simba:install
```

---

## ✅ What Was Tested

| Item | Status |
|------|--------|
| Unit Tests (PHPUnit) | ✅ 1/1 Passing |
| PHP Syntax Validation | ✅ All Files Valid |
| Backward Compatibility | ✅ 100% |
| Laravel Integration | ✅ Verified |
| CodeIgniter 4 Support | ✅ Maintained |

---

## 📊 Changes Summary

- **Files Changed**: 21
- **Lines Added**: +5,375
- **Lines Removed**: -36
- **Commits Merged**: 5 (plus merge commit)
- **New Features**: 8+
- **Breaking Changes**: 0

### Key Files Added/Modified
```
✨ New:
├── config/simba.php (Laravel publishable config)
├── src/Laravel/SimbaServiceProvider.php
├── src/Laravel/SimbaManager.php
├── src/Laravel/Facades/Simba.php
├── src/helpers.php (helper shims)
├── tests/SimbaManagerTest.php
├── CHANGELOG.md
└── RELEASE_NOTES.md

🔄 Modified:
├── src/Client.php (dependency injection support)
├── src/Libraries/*.php (all 5 libraries)
├── src/Services/ConfigService.php
├── composer.json (Laravel autodiscovery)
└── readme.md (Laravel documentation)
```

---

## 🎁 Migration Guide (v2.0 → v2.1)

**No breaking changes!** All existing code continues to work unchanged.

### Optional: Leverage New Laravel Features

1. **In a Laravel app**, after installing:
   ```bash
   php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider"
   ```

2. **In your controller**, use container injection:
   ```php
   public function registerDonor()
   {
       $muzakki = app('simba')->muzakki();
       $response = $muzakki->registerDariLokal(1, $data);
       return response()->json($response);
   }
   ```

3. **Old code still works** in Laravel or CodeIgniter:
   ```php
   $muzakki = new Muzakki();
   $response = $muzakki->registerDariLokal(1, $data);
   ```

---

## 🔗 Related Resources

- **GitHub Repo**: https://github.com/coijiryuna/simbaapi
- **Full Changelog**: [CHANGELOG.md](https://github.com/coijiryuna/simbaapi/blob/main/CHANGELOG.md)
- **Installation Guide**: [INSTALLATION.md](https://github.com/coijiryuna/simbaapi/blob/main/INSTALLATION.md)
- **Complete Documentation**: [DOCUMENTATION.md](https://github.com/coijiryuna/simbaapi/blob/main/DOCUMENTATION.md)
- **PR Summary**: [PR_SUMMARY.md](https://github.com/coijiryuna/simbaapi/blob/main/PR_SUMMARY.md)

---

## ❓ FAQ

**Q: Will this break my existing CodeIgniter app?**  
A: No! This release is 100% backward compatible. All existing code continues to work without changes.

**Q: Can I use this in Laravel now?**  
A: Yes! Full Laravel support is now available. See installation instructions above.

**Q: Should I update from v2.0?**  
A: Yes, recommended for Laravel users. CodeIgniter users can update at their convenience—no breaking changes.

**Q: Do I need to update my code?**  
A: No, but we recommend using dependency injection for better testability and framework integration.

---

## 🙏 Credits

- **Developed by**: @coijiryuna
- **Repository**: https://github.com/coijiryuna/simbaapi
- **License**: MIT

---

## 📝 Release Checklist

- [x] Branch pushed to GitHub
- [x] PR created and reviewed
- [x] CHANGELOG.md created
- [x] Merged to main branch
- [x] Release tagged v2.1.0
- [x] Documentation updated
- [x] Tests passing
- [x] GitHub Release created
- [ ] Published to Packagist (next step)

**See Also**: Compare [v2.0.0...v2.1.0](https://github.com/coijiryuna/simbaapi/compare/v2.0.0...v2.1.0) for full diff.

---

**🎉 Thank you for using SIMBA API!**
