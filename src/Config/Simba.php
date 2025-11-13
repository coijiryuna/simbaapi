<?php

namespace simba\api\Config;

use simba\api\Services\ConfigService;
use CodeIgniter\Config\BaseConfig;

use DateTime;
use Exception;
// Load compat BaseConfig if CodeIgniter version not available
if (!class_exists('CodeIgniter\Config\BaseConfig')) {
    $baseConfigPath = __DIR__ . '/../compat/CodeIgniter/Config/BaseConfig.php';
    if (file_exists($baseConfigPath)) {
        require_once $baseConfigPath;
    }
}

class Simba extends BaseConfig
{
    // Metode untuk mendapatkan konfigurasi dari database
    public static function getBaseUrl()
    {
        return ConfigService::getInstance()->get('base_url', 'https://demo-simba.baznas.or.id/');
    }

    public static function getOrgCode()
    {
        return ConfigService::getInstance()->get('org_code', '9977200');
    }

    public static function getApiKey()
    {
        return ConfigService::getInstance()->get('api_key', 'ZFRNMmVTdFNiMHB6Um1Kc2VtNDJTR3RsVWtoVU9WQkhkVVZFVVVKQlNFWXlkR2xyWVZsVFprcEZTRUZxV2t4TFJqaHNXV2hWTkZsQmEwYzRlRkJxUmtScVVrUTFlamRoWVdObmJGQllRaTgyZVVoUVdYRjJhMDUwZFd0b1QyRkxkVFJxYm5CU2FGaEViRUU5');
    }

    public static function getAdminEmail()
    {
        return ConfigService::getInstance()->get('admin_email', 'baznasprov.demo@baznas.or.id');
    }

    /**
     * Metode static untuk mengubah format tanggal
     * 
     * @param string $inputDate Tanggal dalam format yyyy-mm-dd
     * @return string Tanggal dalam format dd/mm/yyyy
     */
    public static function formatTanggal($inputDate)
    {
        // Periksa apakah tanggal valid
        if (empty($inputDate) || $inputDate == '0000-00-00') {
            return '';
        }

        // Gunakan DateTime untuk konversi
        try {
            $dateObj = new DateTime($inputDate);
            return $dateObj->format('d/m/Y');
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Alternatif metode menggunakan explode
     */
    public static function formatTanggalAlternatif($inputDate)
    {
        // Periksa format input
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $inputDate)) {
            return '';
        }

        // Pecah tanggal
        $parts = explode('-', $inputDate);

        // Kembalikan dalam format baru
        return $parts[2] . '/' . $parts[1] . '/' . $parts[0];
    }

    // Endpoint API
    public static $endpoints = [
        'muzakki_register'      => 'api/ajax_muzaki_register',
        'muzakki_list'          => 'api/ajax_muzaki_list',
        'muzakki_search'        => 'ajax_muzaki_search',
        'total_donasi_muzakki'  => 'api2/ajax_total_donasi_muzaki',
        'detail_donasi_muzakki' => 'api2/ajax_detail_donasi_muzaki',

        'transaksi_simpan'      => 'api/ajax_transaksi_simpan',
        'transaksi_list'        => 'api/ajax_transaksi_list_v2',
        'transaksi_detail'      => 'api/ajax_transaksi_detail',
        'bukti_pembayaran'      => 'api2/bsz',
        'riwayat_donasi'        => 'api2/riwayat_donasi',
        'konfirmasi_donasi'     => 'api2/simpan_konfirmasi_donasi',
        'laporan_donasi'        => 'api2/laporan_donasi',

        'mustahik_register'     => 'api/ajax_mustahik_register',
        'mustahik_search'       => 'api/ajax_mustahik_search',
        'list_mustahik'         => 'api/ajax_mustahik_list',
    ];

    /**
     * Metode static untuk menghilangkan angka desimal dan titik
     * 
     * @param float|string $number Angka yang akan diformat
     * @return int
     */
    public static function removeDecimal($number)
    {
        // Mengonversi ke integer, membuang angka desimal
        return (int)$number;
    }

    /**
     * Metode alternative dengan string manipulation
     * 
     * @param float|string $number Angka yang akan diformat
     * @return string
     */
    public static function removeDecimalToString($number)
    {
        // Memisahkan bagian integer
        return strstr($number, '.', true) ?: $number;
    }

    /**
     * Metode dengan preg_replace
     * 
     * @param float|string $number Angka yang akan diformat
     * @return string
     */
    public static function removeDecimalRegex($number)
    {
        return preg_replace('/\..*/', '', $number);
    }
}
