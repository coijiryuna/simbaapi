<?php

namespace simba\api;

use Config\Services;
use simba\api\Config\Simba;
use simba\api\Exceptions\SimbaApiException;
use simba\api\Services\ResponseFormatter;

/**
 * Client Base untuk API Simba
 * 
 * Class ini adalah base untuk semua library yang akan menggunakan API Simba
 */
class Client
{
    protected $client;
    protected $config;
    protected $timeout;

    public function __construct()
    {
        // Ambil konfigurasi dari file Simba config
        $this->config = new Simba();
        $this->timeout = $this->config->timeout ?? 5; // Default timeout 5 detik
        
        $this->client = Services::curlrequest([
            'timeout' => $this->timeout,
        ]);
    }

    /**
     * Metode umum untuk request API
     * 
     * @param string $endpoint Endpoint API
     * @param array $data Data yang dikirim
     * @param string $method Metode HTTP (GET, POST, PUT, DELETE)
     * @param string $bodyFormat Format body untuk POST/PUT ('form' atau 'json')
     * @return array
     * @throws SimbaApiException
     */
    protected function sendRequest(string $endpoint, array $data = [], string $method = 'POST', string $bodyFormat = 'form'): array
    {
        try {
            // Validasi endpoint
            if (empty($endpoint)) {
                throw new SimbaApiException('Endpoint tidak boleh kosong', 400);
            }

            // Tambahkan parameter wajib
            $data['org'] = $this->config->getOrgCode();
            $data['key'] = $this->config->getApiKey();

            $options = [
                'http_errors' => false,
                'timeout' => $this->timeout
            ];

            $fullUrl = $this->config->getBaseUrl() . $endpoint;

            // If running inside a Laravel app and the Http facade is available, prefer it
            if (class_exists('Illuminate\\Support\\Facades\\Http')) {
                // Build options for Laravel HTTP client
                $laravelOptions = ['timeout' => $this->timeout];

                if ($method === 'GET') {
                    $res = \Illuminate\Support\Facades\Http::withOptions($laravelOptions)->get($fullUrl, $data);
                } else {
                    if ($bodyFormat === 'json') {
                        $res = \Illuminate\Support\Facades\Http::withOptions($laravelOptions)->post($fullUrl, $data);
                    } else {
                        $res = \Illuminate\Support\Facades\Http::asForm()->withOptions($laravelOptions)->post($fullUrl, $data);
                    }
                }

                $statusCode = $res->status();
                $body = $res->body();
            } else {
                // Fallback to existing client (CodeIgniter/Guzzle-like)
                if ($method === 'GET') {
                    $fullUrl .= '?' . http_build_query($data);
                } else {
                    if ($bodyFormat === 'json') {
                        $options['json'] = $data;
                        $options['headers'] = ['Content-Type' => 'application/json'];
                    } else {
                        $options['form_params'] = $data;
                    }
                }

                $response = $this->client->request($method, $fullUrl, $options);

                $statusCode = $response->getStatusCode();
                $body = (string)$response->getBody();
            }

            if ($statusCode >= 200 && $statusCode < 300) {
                $decodedBody = json_decode($body, true);
                
                return ResponseFormatter::success(
                    $decodedBody,
                    'API request successful',
                    $statusCode
                );
            } else {
                $errorMessage = "SIMBA API Error ({$statusCode})";
                log_message('error', $errorMessage . ": {$body}");
                
                return ResponseFormatter::error(
                    "API request failed with status code {$statusCode}",
                    $statusCode,
                    ['raw_response' => $body]
                );
            }
        } catch (SimbaApiException $e) {
            log_message('error', 'SIMBA API Exception: ' . $e->getMessage());
            return $e->toArray();
        } catch (\Exception $e) {
            log_message('error', 'Unexpected Error: ' . $e->getMessage());
            return ResponseFormatter::error(
                $e->getMessage(),
                500
            );
        }
    }

    /**
     * Ambil Configuration
     * 
     * @return Simba
     */
    protected function getConfig(): Simba
    {
        return $this->config;
    }

    /**
     * Helper untuk mengecek apakah response sukses
     * 
     * @param array $response Response dari sendRequest
     * @return bool
     */
    protected function isSuccess(array $response): bool
    {
        return $response['success'] === true && $response['status_code'] >= 200 && $response['status_code'] < 300;
    }

    /**
     * Helper untuk mendapatkan data dari response
     * 
     * @param array $response Response dari sendRequest
     * @return mixed
     */
    protected function getResponseData(array $response)
    {
        return $response['data'] ?? null;
    }
}
