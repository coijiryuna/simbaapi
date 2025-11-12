# Simba API - CodeIgniter 4 Integration Library

Library untuk integrasi dengan SIMBA API BAZNAS (Badan Amil Zakat Nasional) di CodeIgniter 4.

## 📋 Persyaratan

- PHP ^8.0
- CodeIgniter 4
- GuzzleHTTP (via Services::curlrequest)

## 📦 Instalasi

### Melalui Composer

```bash
composer require simba/api
```

### Manual

1. Clone atau download repository ini
2. Tempatkan di folder `app/ThirdParty/simba` atau tempat eksternal lainnya
3. Pastikan autoload sudah terkonfigurasi di `composer.json`

## ⚙️ Konfigurasi

### 1. Tambahkan file konfigurasi di `app/Config/Simba.php`

```php
<?php

namespace Config;

use simba\api\Config\Simba as SimbaConfig;

class Simba extends SimbaConfig
{
    // Override konfigurasi sesuai kebutuhan Anda
}
```

### 2. Set Konfigurasi di Database atau Environment File

Tambahkan konfigurasi di file `.env` atau set melalui database:

```env
SIMBA_BASE_URL=https://demo-simba.baznas.or.id/
SIMBA_ORG_CODE=9977200
SIMBA_API_KEY=your_api_key_here
SIMBA_ADMIN_EMAIL=admin@yourdomain.com
```

## 🛠️ Perintah Spark (Commands)

Library ini menyediakan dua perintah Spark CLI untuk memudahkan instalasi dan setup.

### Command: `simba:publish`

**Fungsi:** Mempublikasikan file konfigurasi dan migrasi ke aplikasi Anda.

```bash
php spark simba:publish
```

**Yang dilakukan:**
- Menyalin `Config/Simba.php` ke `app/Config/Simba.php`
- Menyalin semua migration files ke `app/Database/Migrations/`
- Otomatis menyesuaikan namespace sesuai aplikasi Anda

**Contoh Output:**
```
  created: Config/Simba.php
  created: Database/Migrations/2024-02-03-081118_create_config_table.php
```

**Kapan digunakan:**
- Pertama kali install library
- Ketika ingin menggunakan database config
- Untuk setup di development environment

---

### Command: `simba:install`

**Fungsi:** Setup lengkap untuk production-ready (recommended).

```bash
php spark simba:install
```

**Yang dilakukan:**
1. Jalankan `simba:publish` (publish files)
2. Jalankan `migrate` (create tables)
3. Jalankan `SimbaSeeder` (insert default data)

**Contoh Output:**
```
  created: Config/Simba.php
  created: Database/Migrations/2024-02-03-081118_create_config_table.php

Migration Files in Database Seeder
  [1] 2024-02-03-081118_create_config_table

Running: 2024-02-03-081118_create_config_table
  migrate  batch 1, file: 2024-02-03-081118_create_config_table

Seeding "SimbaSeeder" class
  Inserting 8 seed rows
```

**Kapan digunakan:**
- Fresh installation
- Production deployment
- Ingin setup one-go

---

### Perbandingan Commands

| Aspek | `simba:publish` | `simba:install` |
|-------|---|---|
| Publish Files | ✅ Ya | ✅ Ya |
| Create Tables | ❌ Tidak | ✅ Ya |
| Seed Data | ❌ Tidak | ✅ Ya |
| Waktu Tempuh | ⏱️ ~1 detik | ⏱️ ~2-3 detik |
| Kasus Penggunaan | Development | Production |

---

### Setup Scenarios

**Scenario 1: Quick Production Setup**
```bash
php spark simba:install
# Done! All set up and ready to use
```

**Scenario 2: Manual Step-by-Step**
```bash
# Step 1: Publish configuration
php spark simba:publish

# Step 2: Create tables
php spark migrate

# Step 3: Insert seed data
php spark db:seed simba\\api\\Database\\Seeds\\SimbaSeeder

# All done!
```

**Scenario 3: Development Environment**
```bash
# Just publish, don't setup database yet
php spark simba:publish

# Later, when ready:
php spark simba:install
```

## ⚙️ Konfigurasi (Manual)
```

Atau set di database sesuai dengan struktur di `config.sql`

## 🚀 Penggunaan

### Penggunaan di Controller

```php
<?php

namespace App\Controllers;

use simba\api\Libraries\Muzakki;
use simba\api\Libraries\Mustahik;
use simba\api\Libraries\Pengumpulan;
use simba\api\Libraries\Penyaluran;
use simba\api\Services\ResponseFormatter;

class DonationController extends BaseController
{
    /**
     * Register Donatur (Muzakki)
     */
    public function registerMuzakki()
    {
        $muzakki = new Muzakki();
        
        $data = [
            'nama'      => 'John Doe',
            'handphone' => '08123456789',
            'nik'       => '1234567890123456',
            'email'     => 'john@example.com',
            'alamat'    => 'Jl. Example, No. 123',
            'gender'    => '1',
            'tipe'      => 'perorangan'
        ];
        
        try {
            $response = $muzakki->registerDariLokal(1, $data);
            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Search Muzakki
     */
    public function searchMuzakki()
    {
        $query = $this->request->getPost('q');
        $muzakki = new Muzakki();
        
        try {
            $result = $muzakki->search($query);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON(ResponseFormatter::error($e->getMessage()));
        }
    }

    /**
     * Get Total Donasi
     */
    public function getTotalDonasi()
    {
        $npwz = $this->request->getGet('npwz');
        $tahun = $this->request->getGet('tahun');
        
        $muzakki = new Muzakki();
        
        try {
            $result = $muzakki->getTotalDonasi($npwz, $tahun);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON(ResponseFormatter::error($e->getMessage()));
        }
    }

    /**
     * Get Laporan Donasi
     */
    public function getLaporanDonasi()
    {
        $muzakki = new Muzakki();
        
        $params = [
            'npwz'         => $this->request->getPost('npwz'),
            'dari'         => $this->request->getPost('dari'),
            'hingga'       => $this->request->getPost('hingga')
        ];
        
        try {
            $result = $muzakki->getLaporanDonasi($params);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON(ResponseFormatter::error($e->getMessage()));
        }
    }
}
```

### Contoh Response

Response yang dikembalikan mengikuti format standar:

**Success Response:**
```json
{
    "success": true,
    "status_code": 200,
    "message": "Success",
    "data": {
        // data dari API
    }
}
```

**Error Response:**
```json
{
    "success": false,
    "status_code": 400,
    "message": "Error message",
    "errors": {
        // detail error
    }
}
```

## 📚 Library yang Tersedia

### 1. Muzakki Library

Menangani operasi donatur (muzakki):

```php
$muzakki = new \simba\api\Libraries\Muzakki();

// Register muzakki
$muzakki->registerDariLokal($id, $data);

// Search muzakki
$muzakki->search($query);

// Get list muzakki
$muzakki->getList($params);

// Get total donasi
$muzakki->getTotalDonasi($npwz, $tahun);

// Get laporan donasi
$muzakki->getLaporanDonasi($params);
```

### 2. Mustahik Library

Menangani operasi penerima manfaat (mustahik):

```php
$mustahik = new \simba\api\Libraries\Mustahik();

// Register mustahik
$mustahik->registerDariLokal($id, $data);

// Search mustahik
$mustahik->searchMustahik($query);

// Get list mustahik
$mustahik->getList($params);

// Get kategori mustahik
$mustahik->getKategoriMustahik();

// Validasi kelayakan
$mustahik->validasiKelayakan($data);
```

### 3. Pengumpulan Library

Menangani operasi pengumpulan (transaksi masuk):

```php
$pengumpulan = new \simba\api\Libraries\Pengumpulan();

// Simpan transaksi
$pengumpulan->transaksiDariLokal($id, $data);

// Get list transaksi
$pengumpulan->getListTransaksi($params);

// Get bukti pembayaran
$pengumpulan->getBuktiPembayaran($noTransaksi);

// Get riwayat donasi
$pengumpulan->getRiwayatDonasi($params);
```

### 4. Penyaluran Library

Menangani operasi penyaluran (transaksi keluar):

```php
$penyaluran = new \simba\api\Libraries\Penyaluran();

// Simpan transaksi penyaluran
$penyaluran->simpanTransaksi($data);

// Get list transaksi
$penyaluran->getListTransaksi($params);

// Get daftar program
$penyaluran->getDaftarProgram();

// Get daftar metode penyaluran
$penyaluran->getDaftarVia();
```

## 🔒 Validasi Data

Library menyediakan trait `ValidationTrait` untuk validasi data:

```php
use simba\api\Traits\ValidationTrait;

class MyClass
{
    use ValidationTrait;
    
    public function validate()
    {
        // Validasi NIK
        $this->validateNik('1234567890123456');
        
        // Validasi Email
        $this->validateEmail('user@example.com');
        
        // Validasi Nomor Telepon
        $this->validatePhone('08123456789');
        
        // Validasi Nominal
        $this->validateAmount(500000);
        
        // Validasi Range Tanggal
        $this->validateDateRange('2024-01-01', '2024-12-31');
    }
}
```

## 📝 Response Formatter

Gunakan `ResponseFormatter` untuk response yang konsisten:

```php
use simba\api\Services\ResponseFormatter;

// Success response
ResponseFormatter::success($data, 'Success message', 200);

// Error response
ResponseFormatter::error('Error message', 400, $errors);

// Paginated response
ResponseFormatter::paginated($data, $total, $perPage, $currentPage);

// Validation error
ResponseFormatter::validationError($errors);
```

## 🛠️ Exception Handling

Library menyediakan custom exception `SimbaApiException`:

```php
use simba\api\Exceptions\SimbaApiException;

try {
    // API call
} catch (SimbaApiException $e) {
    $statusCode = $e->getStatusCode();
    $errorData = $e->getErrorData();
    $array = $e->toArray(); // Convert to array
}
```

## 📋 Database Setup

Jalankan migration untuk setup database:

```bash
php spark migrate --namespace simba\\api
```

Atau import file `config.sql` secara manual.

## 🔐 Security Notes

1. **API Key**: Jangan hardcode API key di code. Gunakan environment variables atau database.
2. **Credentials**: Gunakan `.env` file untuk menyimpan kredensial sensitive.
3. **HTTPS**: Pastikan semua komunikasi dengan API menggunakan HTTPS.
4. **Validation**: Selalu validasi input sebelum mengirim ke API.

## 📞 Support

Untuk bantuan lebih lanjut atau melaporkan bug, silakan hubungi tim development.

## 📄 License

MIT License - Lihat file license.md untuk detail lengkap.

## 👨‍💻 Author

**Coiji Ryuna**
- Email: rifacomputerlampung@gmail.com

## 🙏 Credits

Dibuat untuk integrasi sistem SIMBA BAZNAS (Badan Amil Zakat Nasional)
