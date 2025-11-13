<?php

namespace simba\api\Libraries;

use Config\Services;
use simba\api\Client;
use simba\api\Config\Simba;
use simba\api\Traits\ValidationTrait;
use simba\api\Services\ResponseFormatter;

/**
 * Pengumpulan Library untuk API Simba
 * 
 * Library ini menangani operasi pengumpulan (transaksi masuk) di sistem SIMBA
 */
class Pengumpulan extends Client
{
    use ValidationTrait;

    protected $lokalModel;

    public function __construct($lokalModel = null, $httpClient = null)
    {
        parent::__construct($httpClient);
        $this->lokalModel = $lokalModel;
    }

    /**
     * Transaksi dari Database Lokal
     * 
     * @param int $id ID Transaksi di database lokal
     * @return mixed
     */
    public function transaksiDariLokal($id, $data = null)
    {
        // Jika transaksi dikirim dari controller, gunakan
        if ($data) {
            $registrasiData = $this->persiapkanDataTransaksi($data);
            $response = $this->transaksiKeSimba($registrasiData);
            return $this->prosesResponTransaksi($id, $response);
        }
        // Cara lama jika tidak ada transaksi yang dikirim
        if (!$this->lokalModel) {
            throw new \Exception("Model lokal belum diset");
        }
    }


    /**
     * Persiapkan Data Transaksi
     * 
     * @param array $data Data muzakki dari database lokal
     * @return array
     */
    private function persiapkanDataTransaksi($data)
    {
        return [
            'subjek'     => $data['subjek'],
            'tanggal'    => Simba::formatTanggal($data['tanggal']),
            'divisi'     => '22',
            'program'    => $data['program'],
            'via'        => $data['via'],
            'akun'       => $data['akun'],
            'kadar'      => $data['kadar'],
            'jumlah'     => Simba::removeDecimalToString($data['jumlah']),
            'keterangan' => 'BAZ-TGR-' . random_string('numeric', 5),
            'amil'       => $data['amil'] ?? Simba::getAdminEmail(),
            'campaign'   => 0,
            'lokasi'     => '',
            'notif'      => $data['notif'] ?? 'false',

        ];
    }

    /**
     * Ambil List Transaksi Pengumpulan
     * 
     * @param array $params Parameter pencarian
     * @return mixed
     */
    public function getListTransaksi($params = [])
    {
        // Parameter default
        $defaultParams = [
            'tipe' => 'perorangan',
            'keyword' => ''
        ];

        // Gabungkan parameter
        $defaultParams['y'] = $defaultParams['y'] ?? date('Y');
        $defaultParams['p'] = $defaultParams['p'] ?? 1;
        $defaultParams['platform'] = $defaultParams['platform'] ?? 'web';
        $defaultParams['email'] = $defaultParams['email'] ?? Simba::getAdminEmail();
        $queryParams = array_merge($defaultParams, $params);

        // Kirim request list transaksi
        return $this->sendRequest(
            Simba::$endpoints['transaksi_list'],
            $queryParams,
            'GET'
        );
    }

    /**
     * Ambil Bukti Pembayaran
     * 
     * @param string $noTransaksi Nomor Transaksi
     * @param string $jenis Jenis bukti (link/file)
     * @return mixed
     */
    public function getBuktiPembayaran($noTransaksi, $jenis = 'link')
    {
        // Validasi input
        if (empty($noTransaksi)) {
            throw new \Exception("Nomor Transaksi harus diisi");
        }

        // Siapkan parameter
        $params = [
            'notrans' => $noTransaksi,
            'jenis' => $jenis
        ];

        // Kirim request bukti pembayaran
        return $this->sendRequest(
            Simba::$endpoints['bukti_pembayaran'],
            $params,
            'GET'
        );
    }

    /**
     * Generate Nama File Bukti Pembayaran
     * 
     * @param string $noTransaksi Nomor Transaksi
     * @return string
     */
    public function generateNamaFile($noTransaksi)
    {
        return 'Bukti_Pembayaran_' . str_replace('/', '_', $noTransaksi) . '.pdf';
    }

    /**
     * Download Bukti Pembayaran
     * 
     * @param string $noTransaksi Nomor Transaksi
     * @return \CodeIgniter\HTTP\Response
     */
    public function downloadBuktiPembayaran($noTransaksi)
    {
        try {
            // Ambil link bukti pembayaran
            $bukti = $this->getBuktiPembayaran($noTransaksi, 'link');

            if (empty($bukti['link'])) {
                throw new \Exception("Tidak dapat menemukan bukti pembayaran");
            }

            // Unduh file dari link
            $fileContent = file_get_contents($bukti['link']);

            if ($fileContent === false) {
                throw new \Exception("Gagal mengunduh bukti pembayaran");
            }

            // Siapkan response untuk download
            return Services::response()
                ->setContentType('application/pdf')
                ->setBody($fileContent)
                ->setHeader('Content-Disposition', 'attachment; filename="' . $this->generateNamaFile($noTransaksi) . '"');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Ambil Riwayat Donasi
     * 
     * @param array $params Parameter pencarian riwayat donasi
     * @return mixed
     */
    public function getRiwayatDonasi($params = [])
    {
        // Validasi parameter wajib
        $requiredFields = ['npwz', 'periode_dari', 'periode_hingga'];
        foreach ($requiredFields as $field) {
            if (empty($params[$field])) {
                throw new \Exception("Field $field harus diisi");
            }
        }

        // Parameter default
        $defaultParams = ['jenis' => 'npwz', 'email' => ''];

        $defaultParams['tipe'] = $defaultParams['tipe'] ?? 'perorangan';
        $defaultParams['tipe'] = $defaultParams['tipe'] ?? 'perorangan';
        // Gabungkan parameter
        $donasiParams = array_merge($defaultParams, $params);

        // Kirim request riwayat donasi
        return $this->sendRequest(
            Simba::$endpoints['riwayat_donasi'],
            $donasiParams,
            'POST'
        );
    }

    /**
     * Generate Periode Pencarian
     * 
     * @param string $dari Tanggal mulai
     * @param string $hingga Tanggal akhir
     * @return array
     */
    public function generatePeriode($dari = null, $hingga = null)
    {
        // Jika tidak ada parameter, gunakan rentang tahun saat ini
        $dari = $dari ?? '01/01/' . date('Y');
        $hingga = $hingga ?? date('d/m/Y');

        return [
            'periode_dari' => $dari,
            'periode_hingga' => $hingga
        ];
    }

    /**
     * Hitung Total Donasi dari Riwayat
     * 
     * @param array $riwayatDonasi Riwayat donasi
     * @return float
     */
    public function hitungTotalDonasi($riwayatDonasi)
    {
        $total = 0;
        if (!empty($riwayatDonasi['data'])) {
            foreach ($riwayatDonasi['data'] as $donasi) {
                $total += floatval($donasi['jumlah'] ?? 0);
            }
        }
        return $total;
    }

    /**
     * Simpan Konfirmasi Donasi
     * 
     * @param array $konfirmasiData Data konfirmasi donasi
     * @return mixed
     */
    public function simpanKonfirmasiDonasi($konfirmasiData)
    {
        // Validasi data wajib
        $requiredFields = [
            'npwz',
            'asal_bank',
            'bank_tujuan',
            'cara_bayar',
            'tipe_bayar',
            'jumlah',
            'tanggal',
            'rekening_tujuan',
            'hp',
            'email',
            'nama'
        ];

        // Periksa kelengkapan data
        foreach ($requiredFields as $field) {
            if (empty($konfirmasiData[$field])) {
                throw new \Exception("Field $field harus diisi");
            }
        }

        // Validasi lampiran (opsional)
        if (!empty($konfirmasiData['lampiran'])) {
            // Validasi base64 dan ekstensi file
            $this->validasiLampiran($konfirmasiData);
        }

        // Default values
        $konfirmasiData['catatan'] = $konfirmasiData['catatan'] ?? '';
        $konfirmasiData['sapaan'] = $konfirmasiData['sapaan'] ?? 'Bapak/Ibu';

        // Kirim request konfirmasi donasi
        return $this->sendRequest(
            Simba::$endpoints['konfirmasi_donasi'],
            $konfirmasiData
        );
    }

    /**
     * Validasi Lampiran
     * 
     * @param array &$konfirmasiData Data konfirmasi donasi
     */
    private function validasiLampiran(&$konfirmasiData)
    {
        // Ekstensi file yang diizinkan
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'pdf'];

        // Periksa ekstensi
        $ext = strtolower($konfirmasiData['lampiran_ext'] ?? '');
        if (!in_array($ext, $allowedExtensions)) {
            throw new \Exception("Ekstensi file tidak valid. Gunakan: " . implode(', ', $allowedExtensions));
        }

        // Validasi base64
        $base64 = $konfirmasiData['lampiran'];
        if (!$this->isValidBase64($base64)) {
            throw new \Exception("Format base64 lampiran tidak valid");
        }

        // Batasi ukuran file (misalnya maks 5MB)
        $fileSize = strlen(base64_decode($base64));
        if ($fileSize > 5 * 1024 * 1024) {
            throw new \Exception("Ukuran file terlalu besar. Maks 5MB");
        }
    }

    /**
     * Validasi Base64
     * 
     * @param string $base64 String base64
     * @return bool
     */
    private function isValidBase64($base64)
    {
        // Periksa apakah string valid base64
        $decoded = base64_decode($base64, true);
        return $decoded !== false;
    }

    /**
     * Daftar Bank
     * 
     * @return array
     */
    public function getDaftarBank()
    {
        return [
            'BCA' => 'Bank Central Asia',
            'BRI' => 'Bank Rakyat Indonesia',
            'BSI' => 'Bank Syariah Indonesia',
            'Mandiri' => 'Bank Mandiri',
            // Tambahkan bank lainnya
        ];
    }

    /**
     * Daftar Cara Bayar
     * 
     * @return array
     */
    public function getCaraBayar()
    {
        return [
            'ATM' => 'ATM',
            'Mobile Banking' => 'Mobile Banking',
            'Internet Banking' => 'Internet Banking',
            'Teller' => 'Teller',
            'Transfer' => 'Transfer'
        ];
    }

    /**
     * Daftar Tipe Pembayaran
     * 
     * @return array
     */
    public function getTipePembayaran()
    {
        return [
            'Zakat' => 'Zakat',
            'Infak' => 'Infak',
            'Sedekah' => 'Sedekah',
            'Wakaf' => 'Wakaf'
        ];
    }
    /**
     * Ambil Detail Transaksi
     * 
     * @param string $noTransaksi Nomor Transaksi
     * @param string $platform Platform (opsional)
     * @return mixed
     */
    public function getDetailTransaksi($noTransaksi)
    {
        // Validasi input
        if (empty($noTransaksi)) {
            throw new \Exception("Nomor Transaksi harus diisi");
        }

        // Siapkan parameter
        $params = [
            'trans' => $noTransaksi,
        ];

        $params['platform'] = $params['platform'] ?? 'android';
        $params['email'] = $params['email'] ?? Simba::getAdminEmail();
        // Kirim request detail transaksi
        return $this->sendRequest(
            Simba::$endpoints['transaksi_detail'],
            $params,
            'GET'
        );
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

    /**
     * Ambil Status Transaksi
     * 
     * @param string $kodeStatus Kode Status
     * @return string
     */
    public function getStatusTransaksi($kodeStatus)
    {
        $statusMap = [
            '1' => 'Selesai',
            '0' => 'Proses',
            '2' => 'Batal'
        ];

        return $statusMap[$kodeStatus] ?? 'Tidak Dikenal';
    }


    /**
     * Transaksi ke SIMBA
     * 
     * @param array $data Data transaksi
     * @return mixed
     */
    private function transaksiKeSimba($data)
    {
        try {
            $response = $this->sendRequest(
                Simba::$endpoints['transaksi_simpan'],
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
    private function prosesResponTransaksi($id, $response)
    {
        // Validasi respons
        if (!isset($response['status'])) {
            throw new \Exception("Respons dari SIMBA tidak valid");
        }

        switch ($response['status']) {
            case 'Sukses':
                // Update NPWZ di database lokal
                $updateData = [
                    'sink'      => true,
                    'sink_no'   => $response['no_transaksi'],
                    'sink_date' => $response['execdate'],
                ];

                // Update menggunakan model lokal
                $this->lokalModel->update($id, $updateData);

                return [
                    'status'    => 'success',
                    'message'   => 'Transaksi ke SIMBA Berhasil',
                    'sink_no'   => $response['no_transaksi'],
                ];

            case 'Data Exist':
                return [
                    'status'  => 'warning',
                    'message' => 'Transaksi sudah terdaftar di SIMBA',
                    'detail'  => $response
                ];

            case 'Data Invalid':
                return [
                    'status'  => 'danger',
                    'message' => 'Transaksi sudah terdaftar di SIMBA',
                    'detail'  => $response['error']
                ];
            default:
                throw new \Exception("Transaksi gagal: " . ($response['status'] ?? 'Kesalahan tidak diketahui'));
        }
    }

    /**
     * Validasi Kelengkapan Data Transaksi
     * 
     * @param array $data Data Transaksi
     * @return bool
     */
    public function validasiKelengkapanData($data)
    {
        $requiredFields = [
            'subjek',
            'tanggal',
            'program',
            'via',
            'akun',
            'kadar',
            'jumlah'
        ];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return [$data[$field], false];
            }
        }

        return true;
    }
}
