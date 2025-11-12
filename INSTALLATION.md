# 🚀 SIMBA API - INSTALLATION GUIDE

## Step-by-Step Installation

### Step 1: Install via Composer
```bash
composer require simba/api
```

### Step 2: Configure Environment

Edit file `.env` di root project CodeIgniter 4 Anda:

```env
# SIMBA API Configuration
SIMBA_BASE_URL=https://demo-simba.baznas.or.id/
SIMBA_ORG_CODE=9977200
SIMBA_API_KEY=your_api_key_here
SIMBA_ADMIN_EMAIL=admin@yourdomain.com
```

### Step 3: Using Installation Commands

The library provides two convenient Spark commands:

#### Command 1: `simba:publish`
Publishes configuration and migration files to your application.

```bash
php spark simba:publish
```

**What it does:**
- Copies `Config/Simba.php` to `app/Config/Simba.php`
- Copies all migrations to `app/Database/Migrations/`
- Automatically adjusts namespaces for your application

**Output:**
```
  created: Config/Simba.php
  created: Database/Migrations/2024-02-03-081118_create_config_table.php
```

#### Command 2: `simba:install`
Complete installation that sets up everything at once.

```bash
php spark simba:install
```

**What it does:**
1. Publishes configuration and migration files (calls `simba:publish`)
2. Runs all pending migrations (creates `konfigurasi` table)
3. Seeds default configuration data (inserts demo & production configs)

**Output:**
```
  created: Config/Simba.php
  created: Database/Migrations/2024-02-03-081118_create_config_table.php
  
Migration Files in Database Seeder
  [1] 2024-02-03-081118_create_config_table
  
Running: 2024-02-03-081118_create_config_table
  migrate  batch 1, file: 2024-02-03-081118_create_config_table
  
Inserted 8 seed rows
```

#### Quick Setup (Recommended)
```bash
# Just run this one command - it does everything!
php spark simba:install
```

Or step-by-step:
```bash
# Step 1: Publish files
php spark simba:publish

# Step 2: Run migrations
php spark migrate

# Step 3: Seed data
php spark db:seed simba\\api\\Database\\Seeds\\SimbaSeeder
```

### Step 4: Setup Database (Optional)

If you want to use ConfigService for storing configuration in database:

```bash
php spark migrate --namespace simba\\api
```

Or import `config.sql` manually.

### Step 5: Create Configuration File (Optional)

Create `app/Config/Simba.php` if you need custom configuration:

```php
<?php

namespace Config;

use simba\api\Config\Simba as SimbaConfig;

class Simba extends SimbaConfig
{
    // Override default configuration if needed
    public static function getBaseUrl()
    {
        return env('SIMBA_BASE_URL', 'https://demo-simba.baznas.or.id/');
    }
}
```

### Step 6: Start Using

In your controller or service:

```php
<?php

namespace App\Controllers;

use simba\api\Libraries\Muzakki;
use simba\api\Libraries\Mustahik;
use simba\api\Services\ResponseFormatter;

class DonationController extends BaseController
{
    public function registerDonor()
    {
        try {
            $muzakki = new Muzakki();
            
            $data = [
                'nama'      => 'John Doe',
                'handphone' => '08123456789',
                'nik'       => '1234567890123456',
                'email'     => 'john@example.com',
                'alamat'    => 'Jl. Example No. 123'
            ];
            
            $response = $muzakki->registerDariLokal(1, $data);
            
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            return $this->response->setJSON(
                ResponseFormatter::error($e->getMessage())
            );
        }
    }
    
    public function searchMuzakki()
    {
        try {
            $query = $this->request->getPost('q');
            $muzakki = new Muzakki();
            
            $response = $muzakki->search($query);
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            return $this->response->setJSON(
                ResponseFormatter::error($e->getMessage())
            );
        }
    }
}
```

## 🔐 Security Notes

1. **Never commit `.env` file** - Add to `.gitignore`
2. **Use environment variables** for sensitive data
3. **Validate input** before sending to API
4. **Use HTTPS** in production
5. **Rotate API keys** regularly

## 🧪 Testing

### Test Using Commands

```bash
# Test the installation
php spark simba:install

# You should see output showing migrations and seeding completed
```

### Test Using Spark CLI
// Test API Connection
$muzakki = new \simba\api\Libraries\Muzakki();

// Try search
$response = $muzakki->search('test');

// Check response
if ($response['success']) {
    echo "✅ Connection OK!";
    echo json_encode($response['data']);
} else {
    echo "❌ Connection Failed!";
    echo $response['message'];
}
```

## 📝 Common Issues

### Issue: "Undefined function 'helper'"
**Solution**: Make sure you're running CodeIgniter 4. The helper() function should be available globally.

### Issue: "Class not found"
**Solution**: Make sure composer autoloader is included. Run `composer dump-autoload`.

### Issue: "API Key invalid"
**Solution**: Check your `.env` file and make sure SIMBA_API_KEY is correct.

### Issue: "Connection timeout"
**Solution**: Check your internet connection and SIMBA_BASE_URL is correct.

## 📚 Additional Resources

- Full Documentation: [DOCUMENTATION.md](DOCUMENTATION.md)
- Changes Summary: [PERBAIKAN_SUMMARY.md](PERBAIKAN_SUMMARY.md)
- Final Checklist: [FINAL_CHECKLIST.md](FINAL_CHECKLIST.md)

## ✅ Verification Checklist

- [ ] Composer installed
- [ ] SIMBA API credentials obtained
- [ ] `.env` file configured
- [ ] Dependency installed via composer
- [ ] Database migrated (if using ConfigService)
- [ ] First test API call successful
- [ ] Response format matches documentation

## 🆘 Support

If you encounter issues:

1. Check [DOCUMENTATION.md](DOCUMENTATION.md) for detailed guides
2. Review examples in this file
3. Contact: **rifacomputerlampung@gmail.com**

---

**Version**: 2.0.0  
**Last Updated**: November 2024
