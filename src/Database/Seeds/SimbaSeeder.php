<?php

namespace simba\api\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SimbaSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Mengosongkan tabel terlebih dahulu untuk menghindari duplikasi
            // Truncate akan mereset auto-increment ID
            $this->db->table('konfigurasi')->truncate();

            // Data yang akan dimasukkan, diambil dari file config.sql
            $data = [
                [
                    'group' => 'demo',
                    'key'   => 'base_url',
                    'value' => 'https://demo-simba.baznas.or.id/',
                ],
                [
                    'group' => 'demo',
                    'key'   => 'org_code',
                    'value' => '9977200',
                ],
                [
                    'group' => 'demo',
                    'key'   => 'api_key',
                    'value' => 'ZFRNMmVTdFNiMHB6Um1Kc2VtNDJTR3RsVWtoVU9WQkhkVVZFVVVKQlNFWXlkR2xyWVZsVFprbEZTRUZxV2t4TFJqaHNXV2hWTkZsQmEwYzRlRkJxUmtScVVrUTFlamRoWVdObmJGQllRaTgyZVVoUVdYRjJhMDUwZFd0b1QyRkxkVFJxYm5CU2FGaEViRUU5',
                ],
                [
                    'group' => 'demo',
                    'key'   => 'admin_email',
                    'value' => 'baznasprov.demo@baznas.or.id',
                ],
                [
                    'group' => 'simba',
                    'key'   => 'base_url',
                    'value' => 'https://simba.baznas.go.id/',
                ],
                [
                    'group' => 'simba',
                    'key'   => 'org_code',
                    'value' => '3603300',
                ],
                [
                    'group' => 'simba',
                    'key'   => 'api_key',
                    'value' => 'UzBOblVsRlBWVWRtTDBad2FFVm9WM0ZaVUdnek5qSndWRmMzYWxSUmNtZzVkM292Um5sdlltczFkVmRPZEdzd2JXWm5hMUJ3YVRaM05sQjRVbWxLU0VKUWJrdFVVRGxLY2tSeVRqTk1hakp0TlV0TlN6SnhUbTQwZGxSalptaGFSVlY2ZEZSWVdsVjRTMmRRUkdKcGVYWlVaVXBJYzJFMlFWUTRaelpuYW5rPQ==',
                ],
                [
                    'group' => 'simba',
                    'key'   => 'admin_email',
                    'value' => 'baznas.tangerangkab@gmail.com',
                ],
            ];

            // Menggunakan Query Builder untuk memasukkan semua data sekaligus (insertBatch)
            $this->db->table('konfigurasi')->insertBatch($data);
        } catch (\Exception $e) {
            log_message('error', 'SimbaSeeder Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
