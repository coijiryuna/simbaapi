# 🎯 Simba API - CodeIgniter 4 & Laravel Library

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP: ^8.1](https://img.shields.io/badge/PHP-%5E8.1-blue.svg)](https://www.php.net/)
[![Codeigniter: ^4](https://img.shields.io/badge/CodeIgniter-%5E4-red.svg)](https://codeigniter.com/)
[![Laravel: ^8](https://img.shields.io/badge/Laravel-%5E8-F05340.svg)](https://laravel.com/)

Pustaka (library) ini menyediakan cara mudah untuk berinteraksi dengan API Simba BAZNAS RI di dalam aplikasi CodeIgniter 4 dan Laravel.

> **✨ Latest in v2.1.0**: Full Laravel support with auto-detection HTTP client, enhanced error handling, and comprehensive integration guide!

## ⚡ Quick Start

### For CodeIgniter 4

### 1. Install
```bash
composer require simba/api
```

### 2. Configure `.env`
```env
SIMBA_BASE_URL=https://demo-simba.baznas.or.id/
SIMBA_ORG_CODE=9977200
SIMBA_API_KEY=your_api_key
SIMBA_ADMIN_EMAIL=admin@example.com
```

### 3. Use in Controller
```php
<?php
use simba\api\Libraries\Muzakki;

class DonationController extends BaseController
{
    public function registerDonor()
    {
        $muzakki = new Muzakki();
        
        $data = [
            'nama'      => 'John Doe',
            'handphone' => '08123456789',
            'email'     => 'john@example.com'
        ];
        
        $response = $muzakki->registerDariLokal(1, $data);
        return $this->response->setJSON($response);
    }
}
```

### For Laravel

#### Quick Method (⭐ Recommended - Simplest)
```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use simba\api\Libraries\Muzakki;

class DonationController extends Controller
{
    public function registerDonor(Request $request)
    {
        // ✅ Direct instantiation - automatically detects Laravel Http Facade!
        $muzakki = new Muzakki();
        
        $data = [
            'nama'      => $request->input('nama'),
            'handphone' => $request->input('handphone'),
            'email'     => $request->input('email')
        ];
        
        $response = $muzakki->registerDariLokal(1, $data);
        return response()->json($response);
    }
}
```

#### Setup Steps

```bash
# 1. Install
composer require simba/api

# 2. Configure `.env`
SIMBA_BASE_URL=https://demo-simba.baznas.or.id/
SIMBA_ORG_CODE=9977200
SIMBA_API_KEY=your_api_key
SIMBA_ADMIN_EMAIL=admin@example.com

# 3. (Optional) Publish configuration
php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider"

# 4. Start using!
```

#### Alternative Method - Using Service Container
```php
public function registerDonor(Request $request)
{
    // Using Laravel's service container
    $muzakki = app('simba')->muzakki();
    // or using Facade: $muzakki = \Simba::muzakki();
    
    $response = $muzakki->registerDariLokal(1, $data);
    return response()->json($response);
}
```

**📖 Complete Laravel Integration Guide**: See [LARAVEL_INTEGRATION_GUIDE.md](LARAVEL_INTEGRATION_GUIDE.md)

## 📚 Available Libraries

| Library | Purpose |
|---------|---------|
| **Muzakki** | Donatur (Muzakki) Management |
| **Mustahik** | Penerima Manfaat (Mustahik) Management |
| **Pengumpulan** | Pengumpulan (Inbound Transaction) |
| **Penyaluran** | Penyaluran (Outbound Transaction) |
| **Upz** | UPZ (Unit Pengumpul Zakat) Management |

## ✨ Key Features

✅ **Auto-Detecting HTTP Client** - Automatically selects Laravel Http Facade or CodeIgniter Services  
✅ **Laravel Integration** - Works seamlessly with Laravel 8+  
✅ **CodeIgniter Support** - Full CodeIgniter 4 compatibility  
✅ **Response Formatter** - Consistent response format  
✅ **Validation Trait** - Reusable validation logic  
✅ **Exception Handling** - Custom exception classes  
✅ **Service Provider** - Easy dependency injection  
✅ **Type Hints** - Full type-safe methods  
✅ **Error Logging** - Built-in error tracking  
✅ **PSR-4 Autoloading** - Modern PHP standards  

## 📝 Response Format

```json
{
  "success": true,
  "status_code": 200,
  "message": "Success",
  "data": { /* API response */ }
}
```

## 🔒 Validation Available

```php
use simba\api\Traits\ValidationTrait;

$this->validateNik($nik);              // Validate 16-digit NIK
$this->validateEmail($email);           // Validate email format
$this->validatePhone($phone);           // Validate phone number
$this->validateAmount($amount);         // Validate amount
$this->validateDateRange($from, $to);   // Validate date range
$this->validateNokk($nokk);             // Validate 16-digit KK
```

## 📚 File Structure

```
src/
├── Client.php                      # Base HTTP Client (Auto-detecting)
├── ServiceProvider.php             # Service registration
├── Commands/
│   ├── InstallCommand.php         # CLI install command
│   └── PublishCommand.php         # CLI publish command
├── Exceptions/
│   └── SimbaApiException.php      # Custom exceptions
├── Libraries/
│   ├── Muzakki.php                # Donatur management
│   ├── Mustahik.php               # Recipient management
│   ├── Pengumpulan.php            # Inbound transactions
│   ├── Penyaluran.php             # Outbound transactions
│   └── Upz.php                    # UPZ management
├── Services/
│   ├── ResponseFormatter.php      # Response formatting
│   ├── ConfigService.php          # Configuration service
│   └── Laravel/                   # Laravel integration
│       ├── Manager.php            # Service manager
│       ├── Facade.php             # Facade class
│       └── SimbaServiceProvider.php
├── Traits/
│   └── ValidationTrait.php        # Reusable validations
├── Models/
│   └── ApiModel.php               # Database model
└── Config/
    └── Simba.php                  # Main configuration
```

## �️ Available Commands

### Publish Configuration & Migrations
```bash
php spark simba:publish
```
This command publishes the configuration and migration files to your application:
- Copies `Config/Simba.php` to `app/Config/Simba.php`
- Copies migrations to `app/Database/Migrations/`

### Install & Setup Database
```bash
php spark simba:install
```
This command performs complete setup:
1. Publishes configuration and migration files
2. Runs all pending migrations
3. Seeds default configuration data

**Output:**
```
  created: Config/Simba.php
  created: Database/Migrations/2024-02-03-081118_create_config_table.php
  Migration complete
  Seeding data...
```

### Step-by-Step Setup with Commands
```bash
# 1. Publish files
php spark simba:publish

# 2. Setup database (includes migrations + seeding)
php spark simba:install
```

## �📖 Examples

### Register Donatur
```php
use simba\api\Libraries\Muzakki;

$muzakki = new Muzakki();
$response = $muzakki->registerDariLokal(1, [
    'nama'      => 'Andi Wijaya',
    'handphone' => '08123456789',
    'nik'       => '1234567890123456',
    'email'     => 'andi@example.com'
]);

if ($response['success']) {
    echo "NPWZ: " . $response['data']['npwz'];
}
```

### Search Recipient
```php
use simba\api\Libraries\Mustahik;

$mustahik = new Mustahik();
$response = $mustahik->searchMustahik('Budi');

if ($response['success']) {
    foreach ($response['data'] as $item) {
        echo $item['nama'];
    }
}
```

### Get Total Donation
```php
use simba\api\Libraries\Muzakki;

$muzakki = new Muzakki();
$response = $muzakki->getTotalDonasi('NPWZ123', 2024);

if ($response['success']) {
    echo "Total: Rp " . number_format($response['data']['total']);
}
```

### Save Transaction
```php
use simba\api\Libraries\Pengumpulan;

$pengumpulan = new Pengumpulan();
$response = $pengumpulan->transaksiDariLokal(1, [
    'subjek'   => 'NPWZ123',
    'tanggal'  => '2024-01-15',
    'program'  => 'PROGRAM001',
    'via'      => 'VIA001',
    'akun'     => 'AKUN001',
    'jumlah'   => 500000
]);
```

### Distribute Assistance
```php
use simba\api\Libraries\Penyaluran;

$penyaluran = new Penyaluran();
$response = $penyaluran->simpanTransaksi([
    'subjek'   => 'NRM123',        // Mustahik ID
    'tanggal'  => '2024-01-15',
    'program'  => '211010000',     // Konsumtif
    'via'      => '11010101',      // Transfer Bank
    'akun'     => '51010203',      // Zakat
    'jumlah'   => 750000
]);
```

## 🚀 Production Checklist

- ✅ All files syntax validated
- ✅ Type-safe methods
- ✅ Error handling implemented
- ✅ Security best practices
- ✅ Comprehensive documentation
- ✅ Extensible architecture

## 📚 Documentation

For complete documentation and guides:
- **📘 [LARAVEL_INTEGRATION_GUIDE.md](LARAVEL_INTEGRATION_GUIDE.md)** ⭐ - Complete Laravel integration guide with examples
- **📗 [LARAVEL_READY.md](LARAVEL_READY.md)** - Quick start checklist
- **📕 [DOCUMENTATION.md](DOCUMENTATION.md)** - Full API reference
- **📙 [INSTALLATION.md](INSTALLATION.md)** - Installation instructions
- **📓 [PERBAIKAN_SUMMARY.md](PERBAIKAN_SUMMARY.md)** - Changes summary

## 🔗 HTTP Client Priority

The library automatically detects and selects the appropriate HTTP client:

1. **Laravel Http Facade** (Laravel 8+) ← Preferred in Laravel
2. **CodeIgniter Services** (CodeIgniter 4) ← Preferred in CodeIgniter
3. **PHP cURL Extension** ← Fallback
4. **Custom HTTP Client** ← If injected

No manual configuration needed! 🎯

## 📞 Support

For issues or questions:
- **📧 Email**: rifacomputerlampung@gmail.com
- **📖 Documentation**: See [LARAVEL_INTEGRATION_GUIDE.md](LARAVEL_INTEGRATION_GUIDE.md) for troubleshooting

## 📄 License

MIT License - See [license.md](license.md) for details

---

**Version**: 2.1.0  
**Last Updated**: November 2025  
**Status**: ✅ Production Ready  
**Frameworks Tested**: CodeIgniter 4 ✅ | Laravel 8+ ✅

```