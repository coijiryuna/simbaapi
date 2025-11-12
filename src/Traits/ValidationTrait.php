<?php

namespace simba\api\Traits;

/**
 * Validation Trait untuk validasi data
 */
trait ValidationTrait
{
    /**
     * Validasi NIK (16 digit)
     * 
     * @param string $nik NIK yang akan divalidasi
     * @return bool
     * @throws \Exception
     */
    public function validateNik(string $nik, bool $required = true): bool
    {
        if (!$required && empty($nik)) {
            return true;
        }

        if (empty($nik)) {
            throw new \Exception("NIK harus diisi");
        }

        if (strlen($nik) !== 16 || !ctype_digit($nik)) {
            throw new \Exception("NIK harus 16 digit angka");
        }

        return true;
    }

    /**
     * Validasi Nomor KK (16 digit)
     * 
     * @param string $nokk Nomor KK yang akan divalidasi
     * @return bool
     * @throws \Exception
     */
    public function validateNokk(string $nokk, bool $required = false): bool
    {
        if (!$required && empty($nokk)) {
            return true;
        }

        if (empty($nokk)) {
            throw new \Exception("Nomor KK harus diisi");
        }

        if (strlen($nokk) !== 16 || !ctype_digit($nokk)) {
            throw new \Exception("Nomor KK harus 16 digit angka");
        }

        return true;
    }

    /**
     * Validasi Email
     * 
     * @param string $email Email yang akan divalidasi
     * @return bool
     * @throws \Exception
     */
    public function validateEmail(string $email, bool $required = false): bool
    {
        if (!$required && empty($email)) {
            return true;
        }

        if (empty($email)) {
            throw new \Exception("Email harus diisi");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Format email tidak valid");
        }

        return true;
    }

    /**
     * Validasi Nomor HP/Telepon
     * 
     * @param string $phone Nomor telepon yang akan divalidasi
     * @return bool
     * @throws \Exception
     */
    public function validatePhone(string $phone, bool $required = false): bool
    {
        if (!$required && empty($phone)) {
            return true;
        }

        if (empty($phone)) {
            throw new \Exception("Nomor telepon harus diisi");
        }

        $cleanPhone = preg_replace('/\D/', '', $phone);

        if (strlen($cleanPhone) < 9 || strlen($cleanPhone) > 13) {
            throw new \Exception("Nomor telepon harus 9-13 digit");
        }

        return true;
    }

    /**
     * Validasi Nominal (tidak boleh negatif)
     * 
     * @param float|string $amount Nominal yang akan divalidasi
     * @return bool
     * @throws \Exception
     */
    public function validateAmount($amount, bool $required = true): bool
    {
        if (!$required && (empty($amount) || $amount === 0)) {
            return true;
        }

        if (empty($amount)) {
            throw new \Exception("Nominal harus diisi");
        }

        $numAmount = floatval(str_replace(['Rp', '.', ','], '', (string)$amount));

        if ($numAmount < 0) {
            throw new \Exception("Nominal tidak boleh negatif");
        }

        return true;
    }

    /**
     * Validasi Range Tanggal
     * 
     * @param string $startDate Tanggal awal (Y-m-d)
     * @param string $endDate Tanggal akhir (Y-m-d)
     * @return bool
     * @throws \Exception
     */
    public function validateDateRange(string $startDate, string $endDate): bool
    {
        if (empty($startDate) || empty($endDate)) {
            throw new \Exception("Tanggal awal dan akhir harus diisi");
        }

        $start = \DateTime::createFromFormat('Y-m-d', $startDate);
        $end = \DateTime::createFromFormat('Y-m-d', $endDate);

        if (!$start || !$end) {
            throw new \Exception("Format tanggal harus Y-m-d");
        }

        if ($start > $end) {
            throw new \Exception("Tanggal awal tidak boleh lebih besar dari tanggal akhir");
        }

        return true;
    }

    /**
     * Validasi Required Fields
     * 
     * @param array $data Data yang akan divalidasi
     * @param array $requiredFields Daftar field yang wajib ada
     * @return bool
     * @throws \Exception
     */
    public function validateRequiredFields(array $data, array $requiredFields): bool
    {
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            throw new \Exception("Field berikut harus diisi: " . implode(', ', $missingFields));
        }

        return true;
    }
}
