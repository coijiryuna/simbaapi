<?php

namespace simba\api;

/**
 * Service Provider untuk Simba API Library
 * 
 * Registrasi layanan-layanan untuk Simba API
 * 
 * Cara penggunaan di controller:
 * $muzakki = service('simba-muzakki');
 * $mustahik = service('simba-mustahik');
 */
class ServiceProvider
{
    /**
     * Register Muzakki Library
     * 
     * @return \simba\api\Libraries\Muzakki
     */
    public static function muzakki()
    {
        return new Libraries\Muzakki();
    }

    /**
     * Register Mustahik Library
     * 
     * @return \simba\api\Libraries\Mustahik
     */
    public static function mustahik()
    {
        return new Libraries\Mustahik();
    }

    /**
     * Register Pengumpulan Library
     * 
     * @return \simba\api\Libraries\Pengumpulan
     */
    public static function pengumpulan()
    {
        return new Libraries\Pengumpulan();
    }

    /**
     * Register Penyaluran Library
     * 
     * @return \simba\api\Libraries\Penyaluran
     */
    public static function penyaluran()
    {
        return new Libraries\Penyaluran();
    }

    /**
     * Register Upz Library
     * 
     * @return \simba\api\Libraries\Upz
     */
    public static function upz()
    {
        return new Libraries\Upz();
    }

    /**
     * Get Response Formatter Service
     * 
     * @return \simba\api\Services\ResponseFormatter
     */
    public static function responseFormatter()
    {
        return new Services\ResponseFormatter();
    }
}
