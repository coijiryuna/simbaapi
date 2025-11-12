<?php

namespace simba\api\Libraries;

use simba\api\Client;
use simba\api\Config\Simba;
use simba\api\Traits\ValidationTrait;
use simba\api\Services\ResponseFormatter;

/**
 * Muzakki Library untuk API Simba
 * 
 * Library ini menangani operasi donatur (muzakki) di sistem SIMBA
 */
class Muzakki extends Client
{
    use ValidationTrait;

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
     * @param array|null $data Data muzakki
     * @return array
     * @throws \Exception
     */
    public function registerDariLokal($id, $data = null)
    {
        if (!$data && !$this->lokalModel) {
            throw new \Exception("Model lokal belum diset atau data muzakki belum dikirim");
        }

        if ($data) {
            $this->validateMuzakkiData($data);
            $registrasiData = $this->persiapkanDataRegistrasi($data);
            $response = $this->registerKeSimba($registrasiData);
            return $this->prosesResponRegistrasi($id, $response);
        }

        return ResponseFormatter::error('Data muzakki tidak ditemukan');
    }

    /**
     * Persiapkan Data Registrasi
     * 
     * @param array $data Data muzakki dari database lokal
     * @return array
     */
    private function persiapkanDataRegistrasi($data)
    {
        return [
            'nama'       => $data['nama'],
            'alamat'     => $data['alamat'] ?? '',
            'handphone'  => !empty($data['handphone']) ? $data['handphone'] : random_string('numeric', 12),
            'nik'        => !empty($data['nik']) ? $data['nik'] : '0000000000000000',
            'email'      => $data['email'] ?? '',
            'tanggal'    => date('d/m/Y'),
            'tipe'       => $data['tipe'] ?? 'perorangan',
            'gender'     => $data['gender'] ?? '1',
            'amil'       => $data['amil'] ?? Simba::getAdminEmail()
        ];
    }

    /**
     * Validasi Data Muzakki
     * 
     * @param array $muzakkiData Data muzakki
     * @return bool
     * @throws \Exception
     */
    private function validateMuzakkiData($muzakkiData)
    {
        if (empty($muzakkiData['nama'])) {
            throw new \Exception("Nama muzakki harus diisi");
        }

        if (!empty($muzakkiData['nik']) && $muzakkiData['nik'] !== '0000000000000000') {
            $this->validateNik($muzakkiData['nik'], false);
        }

        if (!empty($muzakkiData['email'])) {
            $this->validateEmail($muzakkiData['email'], false);
        }

        if (!empty($muzakkiData['handphone'])) {
            $this->validatePhone($muzakkiData['handphone'], false);
        }

        return true;
    }

    /**
     * Ambil List Muzakki
     * 
     * @param array $params Parameter pencarian
     * @return array
     */
    public function getList($params = [])
    {
        $defaultParams = [
            'p'         => 1,
            'platform'  => 'web',
            'keyword'   => '',
            'tipe'      => 'perorangan',
            'email'     => Simba::getAdminEmail()
        ];

        $queryParams = array_merge($defaultParams, $params);

        return $this->sendRequest(
            Simba::$endpoints['muzakki_list'],
            $queryParams,
            'GET'
        );
    }

    /**
     * Search Muzakki
     * 
     * @param string $query Kata kunci pencarian
     * @param array $params Parameter tambahan
     * @return array
     * @throws \Exception
     */
    public function search($query, $params = [])
    {
        if (empty($query)) {
            throw new \Exception("Kata kunci pencarian harus diisi");
        }

        $searchParams = [
            'v' => $query,
        ];

        if (isset($params['q'])) {
            $searchParams['q'] = $params['q'] ?? 'npwz';
        }

        return $this->sendRequest(
            Simba::$endpoints['muzakki_search'],
            $searchParams,
            'GET'
        );
    }

    /**
     * Ambil Total Donasi Muzakki
     * 
     * @param string $npwz NPWZ muzakki
     * @param string|null $tahun Tahun laporan
     * @return array
     * @throws \Exception
     */
    public function getTotalDonasi($npwz, $tahun = null)
    {
        if (empty($npwz)) {
            throw new \Exception("NPWZ harus diisi");
        }

        $tahun = $tahun ?? date('Y');

        $donasiParams = [
            'npwz' => $npwz,
            'tahun' => $tahun
        ];

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
     * @return array
     * @throws \Exception
     */
    public function getLaporanDonasi($params = [])
    {
        $requiredFields = ['dari', 'hingga', 'npwz'];
        foreach ($requiredFields as $field) {
            if (empty($params[$field])) {
                throw new \Exception("Field $field harus diisi");
            }
        }

        $defaultParams = [
            'jenis' => 'link',
        ];

        $laporanParams = array_merge($defaultParams, $params);

        return $this->sendRequest(
            Simba::$endpoints['laporan_donasi'],
            $laporanParams,
            'POST'
        );
    }

    /**
     * Generate Rentang Tanggal
     * 
     * @param string|null $dari Tanggal mulai
     * @param string|null $hingga Tanggal akhir
     * @return array
     */
    public function generateRentangTanggal($dari = null, $hingga = null)
    {
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
     * Register ke Simba
     * 
     * @param array $data Data registrasi
     * @return array
     */
    protected function registerKeSimba($data)
    {
        return $this->sendRequest(
            Simba::$endpoints['muzakki_register'] ?? 'api/ajax_muzaki_register',
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
            
            if ($this->lokalModel) {
                $this->lokalModel->update($id, [
                    'npwz'            => $responseData['npwz'] ?? null,
                    'tgl_registrasi'  => date('Y-m-d')
                ]);
            }

            return ResponseFormatter::success(
                [
                    'local_id' => $id,
                    'api_response' => $responseData
                ],
                'Registrasi muzakki berhasil'
            );
        }

        return ResponseFormatter::error(
            'Registrasi muzakki gagal',
            $response['status_code'] ?? 400,
            ['api_error' => $response['message'] ?? 'Unknown error']
        );
    }

    /**
     * Validasi Kelengkapan Data Muzakki
     * 
     * @param array $muzakki Data muzakki
     * @return bool
     * @throws \Exception
     */
    public function validasiKelengkapanData($muzakki)
    {
        $requiredFields = [
            'nama',
            'gender'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($muzakki[$field] ?? null)) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            throw new \Exception("Field berikut tidak boleh kosong: " . implode(', ', $missingFields));
        }

        return true;
    }
}
