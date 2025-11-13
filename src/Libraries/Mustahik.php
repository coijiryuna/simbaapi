<?php

namespace simba\api\Libraries;

use simba\api\Config\Simba;
use simba\api\Client;
use simba\api\Traits\ValidationTrait;
use simba\api\Services\ResponseFormatter;

/**
 * Mustahik Library untuk API Simba
 * 
 * Library ini menangani operasi penerima manfaat (mustahik) di sistem SIMBA
 */
class Mustahik extends Client
{
    use ValidationTrait;

    protected $lokalModel;

    public function __construct($lokalModel = null)
    {
        parent::__construct();
        $this->lokalModel = $lokalModel;
    }

    /**
     * Registrasi Mustahik dari Database Lokal
     * 
     * @param int $id ID mustahik di database lokal
     * @param array $mustahik Data mustahik
     * @return array
     * @throws \Exception
     */
    public function registerDariLokal($id, $mustahik = null)
    {
        if (!$this->lokalModel && !$mustahik) {
            throw new \Exception("Model lokal belum diset atau data mustahik belum dikirim");
        }

        // Validasi data mustahik
        if ($mustahik) {
            $this->validateMustahikData($mustahik);
            $registrasiData = $this->persiapkanDataRegistrasi($mustahik);
            $response = $this->registerKeSimba($registrasiData);
            return $this->prosesResponRegistrasi($id, $response);
        }

        return ResponseFormatter::error('Data mustahik tidak ditemukan');
    }

    /**
     * Persiapkan Data Registrasi
     * 
     * @param array $data Data mustahik dari database lokal
     * @return array
     */
    private function persiapkanDataRegistrasi($data)
    {
        return [
            'nama'       => $data['nama'] ?? '',
            'alamat'     => $data['alamat'] ?? '',
            'handphone'  => $data['handphone'] ?? '',
            'nik'        => !empty($data['nik']) ? $data['nik'] : '0000000000000000',
            'email'      => $data['email'] ?? '',
            'nokk'       => !empty($data['nokk']) ? $data['nokk'] : '0000000000000000',
            'pendapatan' => $data['pendapatan'] ?? 0,
            'tanggal'    => date('d/m/Y'),
            'tipe'       => $data['tipe'] ?? 'perorangan',
            'amil'       => $data['amil'] ?? Simba::getAdminEmail()
        ];
    }

    /**
     * Validasi Data Mustahik
     * 
     * @param array $mustahikData Data mustahik
     * @return bool
     * @throws \Exception
     */
    private function validateMustahikData($mustahikData)
    {
        // Validasi NIK (optional dengan default value)
        if (!empty($mustahikData['nik']) && $mustahikData['nik'] !== '0000000000000000') {
            $this->validateNik($mustahikData['nik'], false);
        }

        // Validasi No. KK (optional)
        if (!empty($mustahikData['nokk']) && $mustahikData['nokk'] !== '0000000000000000') {
            $this->validateNokk($mustahikData['nokk'], false);
        }

        // Validasi Email (optional)
        if (!empty($mustahikData['email'])) {
            $this->validateEmail($mustahikData['email'], false);
        }

        // Validasi Telepon (optional)
        if (!empty($mustahikData['handphone'])) {
            $this->validatePhone($mustahikData['handphone'], false);
        }

        // Validasi pendapatan
        if (isset($mustahikData['pendapatan'])) {
            $this->validateAmount($mustahikData['pendapatan'], false);
        }

        return true;
    }

    /**
     * Register ke Simba
     * 
     * @param array $data Data registrasi
     * @return array
     */
    protected function registerKeSimba($data)
    {
        return $this->sendRequest(
            Simba::$endpoints['mustahik_register'] ?? 'api/ajax_mustahik_register',
            $data,
            'POST'
        );
    }

    /**
     * Proses Response Registrasi
     * 
     * @param int $id ID local
     * @param array $response Response dari API
     * @return array
     */
    protected function prosesResponRegistrasi($id, $response)
    {
        if ($this->isSuccess($response)) {
            $responseData = $this->getResponseData($response);
            
            // Update database lokal jika ada model
            if ($this->lokalModel) {
                $this->lokalModel->update($id, [
                    'sink'  => $responseData['status'] ?? 'Sukses',
                    'nrm'   => $responseData['nrm'] ?? null,
                    'idx'   => $responseData['idx'] ?? null
                ]);
            }

            return ResponseFormatter::success(
                [
                    'local_id' => $id,
                    'api_response' => $responseData
                ],
                'Registrasi mustahik berhasil'
            );
        }

        return ResponseFormatter::error(
            'Registrasi mustahik gagal',
            $response['status_code'] ?? 400,
            ['api_error' => $response['message'] ?? 'Unknown error']
        );
    }

    /**
     * Kategori Mustahik
     * 
     * @return array
     */
    public function getKategoriMustahik()
    {
        return [
            'fakir'      => 'Fakir',
            'miskin'     => 'Miskin',
            'amil'       => 'Amil',
            'mualaf'     => 'Mualaf',
            'riqab'      => 'Riqab',
            'gharim'     => 'Gharim',
            'sabilillah' => 'Sabilillah',
            'ibnu_sabil' => 'Ibnu Sabil'
        ];
    }

    /**
     * Validasi Kelayakan Mustahik
     * 
     * @param array $mustahikData Data mustahik
     * @return bool
     */
    public function validasiKelayakan($mustahikData)
    {
        // Contoh sederhana validasi kelayakan
        // Sesuaikan dengan kriteria spesifik organisasi Anda
        $pendapatanMaksimal = 2000000; // Contoh: pendapatan di bawah 2 juta
        $kategoriLayak = ['fakir', 'miskin'];

        return (
            floatval($mustahikData['pendapatan'] ?? 0) <= $pendapatanMaksimal &&
            in_array(strtolower($mustahikData['kategori'] ?? ''), $kategoriLayak)
        );
    }

    /**
     * Search Mustahik
     * 
     * @param string $query Kata kunci pencarian
     * @param array $params Parameter tambahan
     * @return array
     * @throws \Exception
     */
    public function searchMustahik($query, $params = [])
    {
        // Validasi input
        if (empty($query)) {
            throw new \Exception("Kata kunci pencarian harus diisi");
        }

        // Parameter default
        $defaultParams = [
            'v' => $query,
        ];

        // Jika ada parameter tambahan
        if (isset($params['q'])) {
            $defaultParams['q'] = $params['q'] ?? 'nrm';
        }

        // Kirim request pencarian mustahik
        return $this->sendRequest(
            Simba::$endpoints['mustahik_search'] ?? 'api/ajax_mustahik_search',
            $defaultParams,
            'POST'
        );
    }

    /**
     * Ambil List Mustahik
     * 
     * @param array $params Parameter pencarian
     * @return array
     */
    public function getList($params = [])
    {
        // Parameter default
        $defaultParams = [
            'p'        => 1,
            'platform' => 'web',
            'keyword'  => '',
            'tipe'     => 'perorangan',
            'email'    => Simba::getAdminEmail()
        ];

        // Gabungkan parameter
        $queryParams = array_merge($defaultParams, $params);

        // Kirim request list mustahik
        return $this->sendRequest(
            Simba::$endpoints['list_mustahik'] ?? 'api/ajax_mustahik_list',
            $queryParams,
            'GET'
        );
    }

    /**
     * Generate Parameter Pencarian Lanjutan
     * 
     * @param string $query Kata kunci
     * @param string|null $nrm NIK untuk verifikasi (opsional)
     * @return array
     */
    public function generateSearchParams($query, $nrm = null)
    {
        $params = ['v' => $query];

        if (!empty($nrm)) {
            $params['q'] = $nrm;
        }

        return $params;
    }

    /**
     * Validasi Kelengkapan Data Mustahik
     * 
     * @param array $mustahik Data mustahik
     * @return bool
     * @throws \Exception
     */
    public function validasiKelengkapanData($mustahik)
    {
        $requiredFields = [
            'nama',
            'alamat',
            'handphone',
            'nik',
            'tipe',
            'amil',
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($mustahik[$field] ?? null)) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            throw new \Exception("Field berikut tidak boleh kosong: " . implode(', ', $missingFields));
        }

        return true;
    }
}
