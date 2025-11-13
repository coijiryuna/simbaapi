# 🚀 Laravel Integration Guide - SIMBA API v2.1.0

## ✅ Status: Fully Working with Laravel!

SIMBA API v2.1.0 is now **fully compatible with Laravel 8+**. This guide shows you how to use it correctly in your Laravel application.

---

## 📥 Installation

### Step 1: Install via Composer

```bash
composer require simba/api:^2.1
```

### Step 2: Configure (Optional)

If you want to publish the configuration file:

```bash
php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider"
```

This creates `config/simba.php` where you can customize settings.

---

## ✨ Working Integration (Tested and Verified)

The simplest and most reliable way to use SIMBA API in Laravel is **direct instantiation**:

### Method 1: Direct Instantiation (Recommended ⭐)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use simba\api\Libraries\Muzakki;
use simba\api\Config\Simba as SimbaConfig;

class DonationController extends Controller
{
    public function datamuzakki(Request $request)
    {
        try {
            $param = array(
                'p'         => $request->get('page') ?? 1,
                'platform'  => $request->get('platform') ?? 'web',
                'keyword'   => $request->get('keyword') ?? '',
                'tipe'      => $request->get('tipe') ?? 'perorangan',
                'email'     => $request->get('email') ?? SimbaConfig::getAdminEmail(),
            );

            // ✅ Direct instantiation - Simplest and works perfectly
            $result = (new Muzakki())->getList($param);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
```

**Why this works:**
- When you create `new Muzakki()`, it calls `__construct($httpClient = null)`
- With `$httpClient = null`, the Client automatically detects Laravel Http Facade
- It uses Laravel's native HTTP client without any additional setup
- Clean, simple, and reliable ✅

---

## 🔄 All Available Libraries

You can use all 5 SIMBA libraries the same way:

```php
use simba\api\Libraries\Muzakki;
use simba\api\Libraries\Mustahik;
use simba\api\Libraries\Pengumpulan;
use simba\api\Libraries\Penyaluran;
use simba\api\Libraries\Upz;

// Create instances and use
$muzakki = new Muzakki();
$mustahik = new Mustahik();
$pengumpulan = new Pengumpulan();
$penyaluran = new Penyaluran();
$upz = new Upz();

// Then call methods
$muzakki->getList($params);
$mustahik->getList($params);
$pengumpulan->getList($params);
$penyaluran->getList($params);
$upz->getList($params);
```

---

## 📚 Usage Examples

### Example 1: Get Muzakki List

```php
$muzakki = new Muzakki();

$param = [
    'p' => 1,
    'platform' => 'web',
    'keyword' => '',
    'tipe' => 'perorangan',
    'email' => SimbaConfig::getAdminEmail(),
];

$result = $muzakki->getList($param);

if ($result['success']) {
    return response()->json($result['data']);
} else {
    return response()->json(['error' => $result['message']], 400);
}
```

### Example 2: Search Muzakki

```php
$muzakki = new Muzakki();

$result = $muzakki->search('Ahmad Syaiful', ['q' => 'npwz']);

return response()->json($result);
```

### Example 3: Get Total Donation

```php
$muzakki = new Muzakki();

$result = $muzakki->getTotalDonasi('npwz_value', '2024');

return response()->json($result);
```

### Example 4: Get Donation Report

```php
$muzakki = new Muzakki();

$param = [
    'dari' => '01/01/2024',
    'hingga' => '31/12/2024',
    'npwz' => 'npwz_value',
];

$result = $muzakki->getLaporanDonasi($param);

return response()->json($result);
```

---

## 🔧 Advanced: Using Dependency Injection

If you prefer Laravel's dependency injection pattern:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use simba\api\Libraries\Muzakki;

class DonationController extends Controller
{
    protected $muzakki;
    
    // ✅ Laravel will auto-inject Muzakki instance
    public function __construct(Muzakki $muzakki)
    {
        $this->muzakki = $muzakki;
    }
    
    public function datamuzakki(Request $request)
    {
        try {
            $param = [
                'p' => $request->get('page') ?? 1,
                'platform' => $request->get('platform') ?? 'web',
                'keyword' => $request->get('keyword') ?? '',
                'tipe' => $request->get('tipe') ?? 'perorangan',
                'email' => $request->get('email'),
            ];

            // ✅ Use injected instance
            $result = $this->muzakki->getList($param);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
```

**Important:** For this to work, you need to register the ServiceProvider in `config/app.php`:

```php
'providers' => [
    // ... other providers ...
    \simba\api\Laravel\SimbaServiceProvider::class,
],
```

---

## ⚙️ Configuration

### Option 1: Environment Variables

Create or update your `.env` file:

```env
SIMBA_BASE_URL=https://api.baznas.go.id
SIMBA_ORG_CODE=your_org_code
SIMBA_API_KEY=your_api_key
SIMBA_TIMEOUT=5
SIMBA_ADMIN_EMAIL=admin@example.com
```

### Option 2: Publish Config File

```bash
php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider"

php artisan vendor:publish --provider="simba\\api\\Laravel\\SimbaServiceProvider" --tag=config

php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider" --tag="simba-migrations"

php artisan migrate

php artisan vendor:publish --provider="simba\api\Laravel\SimbaServiceProvider" --tag="simba-seeders"

php artisan db:seed --class=SimbaSeeder



```

This creates `config/simba.php`:

```php
return [
    'base_url' => env('SIMBA_BASE_URL', 'https://api.baznas.go.id'),
    'org_code' => env('SIMBA_ORG_CODE'),
    'api_key' => env('SIMBA_API_KEY'),
    'timeout' => env('SIMBA_TIMEOUT', 5),
    'admin_email' => env('SIMBA_ADMIN_EMAIL', 'admin@example.com'),
];
```

---

## 🧪 Testing

Test your integration:

```php
// In your controller or route
Route::get('/test-simba', function () {
    try {
        $muzakki = new Muzakki();
        $result = $muzakki->getList();
        
        return response()->json([
            'success' => true,
            'message' => 'SIMBA API is working!',
            'data' => $result
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});
```

Visit: `http://your-laravel-app.test/test-simba`

---

## 🔍 How It Works

### HTTP Client Selection (Priority Order)

When you instantiate a library (e.g., `new Muzakki()`):

1. **Check for Laravel Http Facade**
   - If available → Use it ✅
   - This is automatic in Laravel 8+

2. **Check for CodeIgniter Services**
   - If available → Use it ✅
   - Useful for hybrid applications

3. **Check for PHP cURL extension**
   - If available → Ready for fallback
   - Safe to proceed

4. **If none available** → Throw error with helpful message

### Request Flow

```
Laravel Controller
    ↓
new Muzakki() 
    ↓
Client::__construct($httpClient = null)
    ↓
Detects Laravel Http Facade
    ↓
Sets $this->client = null (uses facade in sendRequest)
    ↓
getList($param) calls sendRequest()
    ↓
Uses \Illuminate\Support\Facades\Http::get() or ::post()
    ↓
Returns JSON response
    ↓
Controller returns response()->json($result)
```

---

## ⚠️ Common Issues & Solutions

### Issue 1: "HTTP Client tidak tersedia"

**Cause:** Laravel Http Facade not detected

**Solution:** 
- Ensure Laravel 8+ is installed
- Check `php artisan --version`
- Verify `composer.json` has Laravel dependencies

```bash
# Test Laravel Http availability
php artisan tinker
>>> class_exists('Illuminate\Support\Facades\Http')
```

### Issue 2: "Call to undefined method getList()"

**Cause:** Wrong method name or typo

**Solution:** Use exact method names from library:
- `getList()` - Get list
- `search()` - Search
- `getTotalDonasi()` - Get total donation
- `getLaporanDonasi()` - Get report

### Issue 3: API Returns Error Response

**Cause:** Invalid parameters or API error

**Solution:** 
- Check response: `dd($result)`
- Verify parameters match API documentation
- Check API credentials in `.env`

```php
$result = $muzakki->getList($param);

// Always check success flag
if (!$result['success']) {
    dd($result['message']); // Debug the error
}
```

---

## 📚 API Methods Reference

### Muzakki Library

| Method | Parameters | Returns |
|--------|-----------|---------|
| `getList($params)` | `p, platform, keyword, tipe, email` | Array of muzakki |
| `search($query, $params)` | `query, q` | Search results |
| `getTotalDonasi($npwz, $tahun)` | `npwz, tahun` | Total donation amount |
| `getLaporanDonasi($params)` | `dari, hingga, npwz, jenis` | Donation report |

### Mustahik Library

| Method | Parameters | Returns |
|--------|-----------|---------|
| `getList($params)` | `p, platform, keyword, tipe` | Array of mustahik |
| `search($query)` | `query` | Search results |

### Pengumpulan Library

| Method | Parameters | Returns |
|--------|-----------|---------|
| `getList($params)` | `p, platform, keyword` | Array of pengumpulan |

### Penyaluran Library

| Method | Parameters | Returns |
|--------|-----------|---------|
| `getList($params)` | `p, platform, keyword` | Array of penyaluran |

### Upz Library

| Method | Parameters | Returns |
|--------|-----------|---------|
| `getList($params)` | `p, platform, keyword` | Array of upz |

---

## 🎯 Best Practices

✅ **DO:**
- Use direct instantiation: `new Muzakki()`
- Check `$result['success']` before accessing data
- Use try-catch for error handling
- Cache API responses when possible
- Store credentials in `.env`

❌ **DON'T:**
- Hardcode API credentials
- Call API on every page load
- Ignore error messages
- Mix framework APIs (CodeIgniter helpers in Laravel)
- Use deprecated method names

---

## 🚀 Production Checklist

Before deploying to production:

- [ ] API credentials set in `.env`
- [ ] All required parameters configured
- [ ] Error handling implemented
- [ ] API responses cached appropriately
- [ ] Logging implemented for debugging
- [ ] Rate limiting considered
- [ ] Timeout values appropriate

---

## 📞 Support & Troubleshooting

If you encounter issues:

1. **Check error message carefully** - It usually tells you what's wrong
2. **Enable debug mode**: Set `APP_DEBUG=true` in `.env`
3. **Check logs**: `storage/logs/laravel.log`
4. **Verify API credentials**: Ensure `SIMBA_*` vars are set correctly
5. **Test API directly**: Use Postman with your credentials

---

## ✨ Summary

**SIMBA API v2.1.0 is production-ready for Laravel! 🎉**

**Simplest working code:**

```php
$result = (new Muzakki())->getList($param);
return response()->json($result);
```

That's it! The client automatically handles HTTP requests using Laravel's native Http Facade.

Happy coding! 🚀
