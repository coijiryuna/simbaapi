<?php

namespace simba\api\Libraries;

use simba\api\Client;
use simba\api\Config\Simba;

class Penyaluran extends Client
{
    /**
     * Simpan Transaksi Penyaluran
     * 
     * @param array $data Data penyaluran
     * @return mixed
     */
    public function simpanTransaksi($data)
    {
        // Validasi data wajib
        $requiredFields = [
            'subjek',     // NPWZ/ID Mustahik
            'tanggal',    // Tanggal Penyaluran
            'program',    // Kode Program
            'via',        // Metode Penyaluran
            'akun',       // Kode Akun
            'jumlah'      // Nominal Penyaluran
        ];

        // Periksa kelengkapan data
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Field $field harus diisi");
            }
        }

        // Default values
        $data['divisi']     = 23;
        $data['amil']       = $data['amil'] ?? Simba::getAdminEmail();
        $data['notif']      = $data['notif'] ?? true;
        $data['campaign']   = $data['campaign'] ?? 0;
        $data['lokasi']     = $data['lokasi'] ?? '';
        $data['keterangan'] = $data['keterangan'] ?? '';
        $data['kadar']      = $data['kadar'] ?? 0;

        // Kirim request simpan transaksi penyaluran
        return $this->sendRequest(
            Simba::$endpoints['transaksi_simpan'],
            $data
        );
    }

    /**
     * Daftar Program Penyaluran
     * 
     * @return array
     */
    public function getDaftarProgram()
    {
        return [
            '211010000' => 'Penyaluran Konsumtif',
            '211020000' => 'Penyaluran Produktif',
            '211030000' => 'Penyaluran Pendidikan',
            '211040000' => 'Penyaluran Kesehatan',
            // Tambahkan program lainnya sesuai kebutuhan
        ];
    }

    /**
     * Daftar Metode Penyaluran
     * 
     * @return array
     */
    public function getDaftarVia()
    {
        return [
            '11010101' => 'Transfer Bank',
            '11010102' => 'Tunai',
            '11010103' => 'Bantuan Langsung'
        ];
    }

    /**
     * Daftar Akun Penyaluran
     * 
     * @return array
     */
    public function getDaftarAkun()
    {
        return [
            '51010203' => 'Penyaluran Zakat',
            '51010204' => 'Penyaluran Infak',
            '51010205' => 'Penyaluran Sedekah'
        ];
    }

    /**
     * Validasi Penyaluran
     * 
     * @param array $data Data penyaluran
     * @return bool
     */
    public function validasiPenyaluran($data)
    {
        // Contoh validasi sederhana
        $data = 5000000; // Contoh: maks penyaluran 5 juta

        // Cek jumlah penyaluran
        if (floatval($data['jumlah']) > $data) {
            throw new \Exception("Jumlah penyaluran melebihi batas maksimal");
        }

        return true;
    }

    /**
     * Ambil List Transaksi Penyaluran
     * 
     * @param array $params Parameter pencarian
     * @return mixed
     */
    public function getListTransaksi($params = [])
    {
        // Parameter default
        $defaultParams = [
            'platform' => 'web'
        ];
        $defaultParams['p'] = $defaultParams['p'] ?? 1;
        $defaultParams['email'] = $defaultParams['email'] ?? Simba::getAdminEmail();
        $defaultParams['tipe'] = $defaultParams['tipe'] ?? 'perorangan';
        $defaultParams['email'] = $defaultParams['email'] ?? 0;
        $defaultParams['y'] = $defaultParams['y'] ?? \date('Y');
        $defaultParams['keyword'] = $defaultParams['keyword'] ?? '';
        // Gabungkan parameter
        $queryParams = array_merge($defaultParams, $params);

        // Kirim request list transaksi penyaluran
        return $this->sendRequest(
            Simba::$endpoints['transaksi_penyaluran_list'],
            $queryParams,
            'GET'
        );
    }

    /**
     * Hitung Total Penyaluran
     * 
     * @param array $listTransaksi Hasil list transaksi
     * @return float
     */
    public function hitungTotalPenyaluran($listTransaksi)
    {
        $total = 0;
        if (!empty($listTransaksi['data'])) {
            foreach ($listTransaksi['data'] as $transaksi) {
                $total += floatval($transaksi['jumlah'] ?? 0);
            }
        }
        return $total;
    }

    /**
     * Generate Filter Tambahan
     * 
     * @return array
     */
    public function getFilterOptions()
    {
        return [
            'tipe' => [
                'perorangan' => 'Perorangan',
                'lembaga' => 'Lembaga'
            ],
            'program' => $this->getDaftarProgram(),
            'via' => $this->getDaftarVia()
        ];
    }

    /**
     * Format Mata Uang
     * 
     * @param float $nominal Nominal
     * @return string
     */
    public function formatRupiah($nominal)
    {
        return 'Rp. ' . number_format($nominal, 0, ',', '.');
    }
}
