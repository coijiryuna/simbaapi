<?php

namespace simba\api\Models;

use CodeIgniter\Model;

class ApiModel extends Model
{
    protected $table            = 'konfigurasi'; // Sesuaikan dengan nama tabel Anda
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['group', 'key', 'value', 'created_at', 'updated_at'];

    /**
     * Ambil Konfigurasi SIMBA
     * 
     * @return array
     */
    public function getSimbaConfig()
    {
        // Tentukan grup berdasarkan lingkungan
        $group = (getenv('CI_ENVIRONMENT') === 'production') ? 'simba' : 'demo';

        // Ambil konfigurasi SIMBA dari database
        $config = $this->where('group', $group)->findAll();

        // Konversi ke array asosiatif
        $simbaConfig = [];
        foreach ($config as $item) {
            $simbaConfig[$item['key']] = $item['value'];
        }

        return $simbaConfig;
    }
}
