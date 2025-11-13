<?php

return [
    /*
     * Default base url for Simba API
     */
    'base_url' => 'https://demo-simba.baznas.or.id/',

    'org_code' => '9977200',

    'api_key' => 'ZFRNMmVTdFNiMHB6Um1Kc2VtNDJTR3RsVWtoVU9WQkhkVVZFVVVKQlNFWXlkR2xyWVZsVFprcEZTRUZxV2t4TFJqaHNXV2hWTkZsQmEwYzRlRkJxUmtScVVrUTFlamRoWVdObmJGQllRaTgyZVVoUVdYRjJhMDUwZFd0b1QyRkxkVFJxYm5CU2FGaEViRUU5',

    'admin_email' => 'baznasprov.demo@baznas.or.id',

    'endpoints' => [
        'muzakki_register'      => 'api/ajax_muzaki_register',
        'muzakki_list'          => 'api/ajax_muzaki_list',
        'muzakki_search'        => 'ajax_muzaki_search',
        'total_donasi_muzakki'  => 'api2/ajax_total_donasi_muzakki',
        'detail_donasi_muzakki' => 'api2/ajax_detail_donasi_muzakki',

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
    ],
];
