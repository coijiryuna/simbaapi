<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimbaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::table('konfigurasi')->truncate();

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
                    'value' => '==',
                ],
                [
                    'group' => 'simba',
                    'key'   => 'admin_email',
                    'value' => 'baznas.tangerangkab@gmail.com',
                ],
            ];


            DB::table('konfigurasi')->insert($data);
        } catch (\Exception $e) {
            Log::error('SimbaSeeder (Laravel) Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
