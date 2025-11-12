<?php

namespace simba\api\Libraries;

use simba\api\Client;
use simba\api\Config\Simba;

class Upz extends Client
{

    protected $lokalModel;

    public function __construct($lokalModel = null)
    {
        parent::__construct();
        $this->lokalModel = $lokalModel;
    }

    /**
     * Registrasi Muzakki dari Database Lokal
     * 
     * @param int $id ID muzakki di database lokal
     * @return mixed
     */
    public function registerDariLokal($id)
    {
        // Validasi model lokal
        if (!$this->lokalModel) {
            throw new \Exception("Model lokal belum diset");
        }

        // Cari data muzakki di database lokal
        $muzakki = $this->lokalModel->find($id);

        if (!$muzakki) {
            throw new \Exception("Muzakki dengan ID {$id} tidak ditemukan");
        }

        // Siapkan data untuk registrasi
        $registrasiData = $this->persiapkanDataRegistrasi($muzakki);

        // Lakukan registrasi ke SIMBA
        $response = $this->registerKeSimba($registrasiData);

        // Proses respons
        return $this->prosesResponRegistrasi($id, $response);
    }

    /**
     * Persiapkan Data Registrasi
     * 
     * @param object $upz Data UPZ dari database lokal
     * @return array
     */
    private function persiapkanDataRegistrasi($upz)
    {
        return [
            'nama'       => $upz['nama'],
            'alamat'     => $upz['alamat'] ?? '',
            'handphone'  => !empty($upz['handphone']) ? $upz['handphone'] : random_string('numeric', 12),
            'nik'        => !empty($upz['nik']) ? $upz['nik'] : '0000000000000000',
            'email'      => $upz['email'] ?? '',
            'tanggal'    => !empty($upz['tanggal']) ? $upz['tanggal'] : date('d/m/Y'),
            'tipe'       => $upz['tipe'] ?? 'lembaga',
            'gender'     => $upz['gender'] ?? '1',
            'verifikasi' => 'handphone',
            'amil'       => $data['amil'] ?? Simba::getAdminEmail()
        ];
    }

    /**
     * Ambil List Muzakki
     * 
     * @param array $params Parameter pencarian
     * @return mixed
     */
    public function getList($params = [])
    {
        // Parameter default
        $defaultParams = [
            'p' => 1,  // Halaman pertama
            'platform' => 'web',
            'keyword' => '',
            'tipe' => 'perorangan'
        ];

        $defaultParams['amil'] = $defaultParams['amil'] ?? Simba::getAdminEmail();
        // Gabungkan parameter
        $queryParams = array_merge($defaultParams, $params);

        // Kirim request list muzakki
        return $this->sendRequest(
            Simba::$endpoints['muzakki_list'],
            $queryParams,
            'GET'
        );
    }

    public function search($query)
    {

        // Validasi input
        if (empty($query)) {
            throw new \Exception("Kata kunci pencarian harus diisi");
        }
        // Siapkan parameter pencarian
        $searchParams = [
            'v' => $query,  // Kata kunci
        ];
        // Tambahkan verifikasi jika disediakan
        $searchParams['q'] = $searchParams['q'] ?? 'npwz';
        // Kirim request pencarian muzakki
        return $this->sendRequest(
            Simba::$endpoints['muzakki_search'],
            $searchParams,
            'GET'
        );
    }

    public function getTotalDonasi($npwz, $tahun = null)
    {
        // Validasi input
        if (empty($npwz)) {
            throw new \Exception("NPWZ harus diisi");
        }

        // Gunakan tahun saat ini jika tidak disediakan
        $tahun = $tahun ?? date('Y');

        // Siapkan parameter
        $donasiParams = [
            'npwz' => $npwz,
            'tahun' => $tahun
        ];

        // Kirim request total donasi
        return $this->sendRequest(
            Simba::$endpoints['total_donasi_muzakki'],
            $donasiParams,
            'POST'
        );
    }

    /**
     * Ambil Laporan Donasi
     * 
     * @param array $params Parameter laporan donasi
     * @return mixed
     */
    public function getLaporanDonasi($params = [])
    {
        // Validasi parameter wajib
        $requiredFields = ['dari', 'hingga', 'npwz'];
        foreach ($requiredFields as $field) {
            if (empty($params[$field])) {
                throw new \Exception("Field $field harus diisi");
            }
        }

        // Parameter default
        $defaultParams = [
            'jenis' => 'link',
        ];

        // Gabungkan parameter
        $laporanParams = array_merge($defaultParams, $params);

        // Kirim request laporan donasi
        return $this->sendRequest(
            Simba::$endpoints['laporan_donasi'],
            $laporanParams,
            'POST'
        );
    }

    /**
     * Generate Rentang Tanggal
     * 
     * @param string $dari Tanggal mulai
     * @param string $hingga Tanggal akhir
     * @return array
     */
    public function generateRentangTanggal($dari = null, $hingga = null)
    {
        // Jika tidak ada parameter, gunakan bulan saat ini
        $dari = $dari ?? '01/' . date('m/Y');
        $hingga = $hingga ?? date('t/m/Y');

        return [
            'dari' => $dari,
            'hingga' => $hingga
        ];
    }

    /**
     * Hitung Total Donasi dari Laporan
     * 
     * @param array $laporanDonasi Laporan donasi
     * @return float
     */
    public function hitungTotalDonasi($laporanDonasi)
    {
        $total = 0;
        if (!empty($laporanDonasi['data'])) {
            foreach ($laporanDonasi['data'] as $donasi) {
                $total += floatval($donasi['jumlah'] ?? 0);
            }
        }
        return $total;
    }

    /**
     * Generate Laporan dalam Format CSV
     * 
     * @param array $laporanDonasi Laporan donasi
     * @return string
     */
    public function generateLaporanCSV($laporanDonasi)
    {
        $csv = "Tanggal,Jumlah,Program,Via\n";

        if (!empty($laporanDonasi['data'])) {
            foreach ($laporanDonasi['data'] as $donasi) {
                $csv .= implode(',', [
                    $donasi['tanggal'] ?? '',
                    $donasi['jumlah'] ?? 0,
                    $donasi['program'] ?? '',
                    $donasi['via'] ?? ''
                ]) . "\n";
            }
        }

        return $csv;
    }

    /**
     * Registrasi ke SIMBA
     * 
     * @param array $data Data registrasi
     * @return mixed
     */
    private function registerKeSimba($data)
    {
        try {
            $response = $this->sendRequest(
                Simba::$endpoints['muzakki_register'],
                $data,
                'POST'
            );

            return $response;
        } catch (\Exception $e) {
            throw new \Exception("Gagal mengirim data ke SIMBA: " . $e->getMessage());
        }
    }

    /**
     * Proses Respons Registrasi
     * 
     * @param int $id ID muzakki lokal
     * @param array $response Respons dari SIMBA
     * @return mixed
     */
    private function prosesResponRegistrasi($id, $response)
    {
        // Validasi respons
        if (!isset($response['status'])) {
            throw new \Exception("Respons dari SIMBA tidak valid");
        }

        switch ($response['status']) {
            case 'Sukses':
                // Update NPWZ di database lokal
                $updateData = [
                    'npwz' => $response['npwz'],
                    'tgl_registrasi' => date('Y-m-d')
                ];

                // Update menggunakan model lokal
                $this->lokalModel->update($id, $updateData);

                return [
                    'status'    => 'success',
                    'message'   => 'Registrasi Muzaki ke SIMBA Berhasil',
                    'npwz'      => $response['npwz']
                ];

            case 'Data Exist':
                return [
                    'status'    => 'warning',
                    'message'   => 'Muzaki sudah terdaftar di SIMBA',
                    'detail'    => $response
                ];

            default:
                throw new \Exception("Registrasi gagal: " . ($response['message'] ?? 'Kesalahan tidak diketahui'));
        }
    }

    /**
     * Validasi Kelengkapan Data Muzakki
     * 
     * @param object $muzakki Data muzakki
     * @return bool
     */
    public function validasiKelengkapanData($muzakki)
    {
        $requiredFields = [
            'nama_upz'
        ];

        foreach ($requiredFields as $field) {
            if (empty($muzakki->$field)) {
                return false;
            }
        }

        return true;
    }

}
