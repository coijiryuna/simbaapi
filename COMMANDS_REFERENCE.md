# 🛠️ SIMBA API - COMMANDS REFERENCE

Panduan lengkap penggunaan Spark commands untuk Simba API Library.

## 📋 Daftar Commands

| Command | Fungsi | Waktu |
|---------|--------|-------|
| `simba:publish` | Publish configuration & migrations | ~1s |
| `simba:install` | Full setup (publish + migrate + seed) | ~2-3s |

---

## 🚀 Command 1: `simba:publish`

### Deskripsi
Mempublikasikan file konfigurasi dan database migration files ke aplikasi Anda.

### Syntax
```bash
php spark simba:publish
```

### Yang Dilakukan
1. ✅ Menyalin `src/Config/Simba.php` → `app/Config/Simba.php`
2. ✅ Menyalin `src/Database/Migrations/*` → `app/Database/Migrations/`
3. ✅ Otomatis menyesuaikan namespace ke aplikasi Anda
4. ✅ Membuat direktori jika belum ada

### Contoh Output
```
  created: Config/Simba.php
  created: Database/Migrations/2024-02-03-081118_create_config_table.php
```

### Konfigurasi yang Dipublikasikan

**File 1: Config/Simba.php**
```php
// Endpoint API
'muzakki_register'        => '/muzakki/register',
'muzakki_search'          => '/muzakki/search',
'mustahik_register'       => '/mustahik/register',
'mustahik_search'         => '/mustahik/search',
// ... dan 11 endpoint lainnya
```

**File 2: Database/Migrations**
- `2024-02-03-081118_create_config_table.php`
  - Creates table: `konfigurasi`
  - Columns: id, group, key, value, created_at, updated_at

### Kasus Penggunaan

✅ Fresh installation  
✅ Development setup  
✅ Ketika ingin custom configuration  
✅ Setup tanpa database seeding  

### Contoh Penggunaan

```bash
# Basic usage
php spark simba:publish

# Kemudian setup database manual
php spark migrate
php spark db:seed simba\\api\\Database\\Seeds\\SimbaSeeder
```

---

## 🔧 Command 2: `simba:install`

### Deskripsi
Setup lengkap library dalam satu command. Ini adalah rekomendasi untuk production deployment.

### Syntax
```bash
php spark simba:install
```

### Yang Dilakukan
1. ✅ Jalankan `simba:publish` (publish files)
2. ✅ Jalankan `migrate` (create konfigurasi table)
3. ✅ Jalankan `SimbaSeeder` (insert default config)

### Contoh Output
```
  created: Config/Simba.php
  created: Database/Migrations/2024-02-03-081118_create_config_table.php

Migration Files in Database Seeder
  [1] 2024-02-03-081118_create_config_table

Running: 2024-02-03-081118_create_config_table
  migrate  batch 1, file: 2024-02-03-081118_create_config_table

Seeding "SimbaSeeder" class
  Inserted 8 rows (4 demo + 4 production config)
```

### Data yang Di-seed

**Group: DEMO**
```
base_url     = https://demo-simba.baznas.or.id/
org_code     = 9977200
api_key      = [base64 encoded demo key]
admin_email  = baznasprov.demo@baznas.or.id
```

**Group: SIMBA (Production)**
```
base_url     = https://simba.baznas.go.id/
org_code     = 3603300
api_key      = [base64 encoded production key]
admin_email  = baznas.tangerangkab@gmail.com
```

### Error Handling

Jika ada error, command akan:
1. ✅ Logging error ke `writable/logs/`
2. ✅ Throw exception untuk debugging
3. ✅ Menampilkan pesan error yang jelas

Contoh:
```
ERROR: SimbaSeeder Error: Table 'konfigurasi' doesn't exist
```

### Kasus Penggunaan

✅ Fresh installation  
✅ Production deployment  
✅ Setup lengkap one-go  
✅ Testing environment  

### Contoh Penggunaan

```bash
# Production setup (recommended)
php spark simba:install

# Konfigurasi otomatis tersedia dari database
# Menggunakan default demo account

# Update konfigurasi di .env atau database
SIMBA_BASE_URL=https://simba.baznas.go.id/
SIMBA_ORG_CODE=3603300
SIMBA_API_KEY=your_production_key
```

---

## 📊 Perbandingan Setup Methods

### Method 1: Quick Install (Recommended)
```bash
php spark simba:install
```
- ✅ Semua setup otomatis
- ✅ Tidak ada step manual
- ⏱️ ~2-3 detik
- 👍 Untuk production

### Method 2: Step-by-Step
```bash
php spark simba:publish
php spark migrate
php spark db:seed simba\\api\\Database\\Seeds\\SimbaSeeder
```
- ✅ Kontrol penuh
- ✅ Debug lebih mudah
- ⏱️ ~3-5 detik
- 👍 Untuk development

### Method 3: Manual Import
```bash
# Import config.sql manually
# Copy files manually
# Edit composer.json autoload manually
```
- ✅ Fleksibel penuh
- ❌ Rawan error
- ⏱️ ~5+ menit
- 👎 Not recommended

---

## 🔍 Troubleshooting Commands

### Issue 1: Command tidak dikenali

```bash
$ php spark simba:install
Command "simba:install" not found.
```

**Solusi:**
```bash
# Update composer autoload
composer dump-autoload

# Cek apakah library sudah terinstall
composer show simba/api

# Try again
php spark simba:install
```

### Issue 2: Permission denied

```bash
$ php spark simba:publish
Unable to determine the correct source directory
```

**Solusi:**
```bash
# Pastikan direktori writable
chmod -R 755 writable/
chmod -R 755 app/

# Try again
php spark simba:publish
```

### Issue 3: Table already exists

```bash
ERROR: Table 'konfigurasi' already exists
```

**Solusi:**
```bash
# Rollback migrations
php spark migrate:rollback --batch 1

# Try install again
php spark simba:install
```

### Issue 4: Namespace error

```bash
ERROR: Class 'simba\api\Database\Seeds\SimbaSeeder' not found
```

**Solusi:**
```bash
# Update composer autoload
composer dump-autoload

# Verify file exists
ls -la src/Database/Seeds/SimbaSeeder.php

# Try again
php spark simba:install
```

---

## ✅ Verification

Setelah menjalankan commands, verifikasi setup:

### 1. Check Config File
```bash
ls -la app/Config/Simba.php
```
Seharusnya ada dan readable.

### 2. Check Migration File
```bash
ls -la app/Database/Migrations/2024-02-03-081118_create_config_table.php
```
Seharusnya ada dan readable.

### 3. Check Database Table
```bash
php spark tinker
```

```php
>>> \Config\Database::connect()->table('konfigurasi')->countAll()
// Should return 8 (4 demo + 4 production)

>>> \Config\Database::connect()->table('konfigurasi')->get()->getResult()
// Show all configuration records
```

### 4. Test in Controller
```php
<?php

namespace App\Controllers;

use simba\api\Services\ConfigService;

class TestController extends BaseController
{
    public function testConfig()
    {
        $baseUrl = ConfigService::get('demo', 'base_url');
        dd($baseUrl); // Should show: https://demo-simba.baznas.or.id/
    }
}
```

---

## 🎯 Recommended Setup Flow

```
┌─────────────────────────────────┐
│ 1. composer require simba/api   │
└────────────┬────────────────────┘
             ↓
┌─────────────────────────────────┐
│ 2. php spark simba:install      │
│    (Do everything at once!)     │
└────────────┬────────────────────┘
             ↓
┌─────────────────────────────────┐
│ 3. php spark tinker             │
│    (Verify in database)         │
└────────────┬────────────────────┘
             ↓
┌─────────────────────────────────┐
│ 4. Update .env config           │
│    (Set production credentials) │
└────────────┬────────────────────┘
             ↓
┌─────────────────────────────────┐
│ 5. Start using library!         │
│    (Ready for production!)      │
└─────────────────────────────────┘
```

---

## 📚 Related Documentation

- Full Guide: [DOCUMENTATION.md](DOCUMENTATION.md)
- Installation: [INSTALLATION.md](INSTALLATION.md)
- Quick Start: [readme.md](readme.md)
- Changes: [PERBAIKAN_SUMMARY.md](PERBAIKAN_SUMMARY.md)

---

## 💡 Tips & Best Practices

1. **Always use `simba:install` untuk production** - Lebih aman dan terjamin
2. **Run `php spark list | grep simba`** - Untuk lihat available commands
3. **Check logs di `writable/logs/`** - Jika ada error
4. **Backup database** - Sebelum rollback migrations
5. **Update composer regularly** - Untuk latest version

---

## 📞 Support

Questions or issues? Contact: **rifacomputerlampung@gmail.com**

---

**Version**: 2.0.0  
**Last Updated**: November 2024  
**Status**: ✅ Production Ready

