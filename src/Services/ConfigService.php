<?php

namespace simba\api\Services;

use simba\api\Models\ApiModel;

class ConfigService
{
    private static $instance = null;
    private $config = [];
    public static $isProduction; // Default ke development

    private function __construct()
    {
        $model = new ApiModel();
        // Tentukan grup berdasarkan lingkungan dari file .env. Gunakan env() yang lebih andal untuk CodeIgniter.
        $group = (env('CI_ENVIRONMENT') === 'production') ? 'simba' : 'demo';

        // Ambil semua konfigurasi untuk grup yang sesuai
        $configsFromDb = $model->where('group', $group)->findAll();

        // Ubah menjadi format array asosiatif [key => value]
        foreach ($configsFromDb as $conf) {
            $this->config[$conf['key']] = $conf['value'];
        }
    }

    /**
     * Singleton instance
     * 
     * @return KonfigurasiService
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Ambil nilai konfigurasi
     * 
     * @param string $key Kunci konfigurasi
     * @param mixed $default Nilai default jika tidak ditemukan
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Update konfigurasi
     * 
     * @param string $key Kunci konfigurasi
     * @param mixed $value Nilai baru
     */
    public function set($key, $value)
    {
        $model = new ApiModel();
        // Tentukan grup berdasarkan lingkungan. Gunakan env() yang lebih andal untuk CodeIgniter.
        $group = (env('CI_ENVIRONMENT') === 'production') ? 'simba' : 'demo';

        // Ambil konfigurasi SIMBA dari database
        $config = $model->where('group', $group)->where('key', $key)->first();

        if ($config) {
            $model->update($config['id'], ['value' => $value]);
        } else {
            // Perbaikan: Gunakan variabel $group yang dinamis saat insert
            $model->insert([
                'group' => $group,
                'key'   => $key,
                'value' => $value
            ]);
        }

        // Update instance config
        $this->config[$key] = $value;
    }
}
