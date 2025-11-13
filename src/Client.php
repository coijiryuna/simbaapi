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

    /**
     * Client constructor.
     *
     * @param mixed|null $httpClient Optional HTTP client to use (Laravel Http factory or similar). If null, falls back to CodeIgniter Services::curlrequest().
     */
    public function __construct($httpClient = null)
    {
        // Ambil konfigurasi dari file Simba config
        $this->config = new Simba();
        $this->timeout = $this->config->timeout ?? 5; // Default timeout 5 detik

        if ($httpClient !== null) {
            // Use injected client (expected: Illuminate\Http\Client\Factory or compatible)
            $this->client = $httpClient;
            return;
        }

        // Priority 1: Check if Laravel Http facade is available
        if (class_exists('\Illuminate\Support\Facades\Http')) {
            $this->client = null; // We'll use the facade directly in sendRequest when $this->client is null
            return;
        }

        // Priority 2: Try CodeIgniter Services if available
        if (class_exists('Config\Services')) {
            try {
                $this->client = Services::curlrequest([
                    'timeout' => $this->timeout,
                ]);
                return;
            } catch (\Exception $e) {
                // Services not available, will use fallback
                $this->client = null;
                return;
            }
        }

        // Priority 3: Try using native PHP cURL if available
        if (extension_loaded('curl')) {
            $this->client = null; // Will use cURL directly via Laravel Http facade or error
            return;
        }

        // If nothing is available, set to null and let sendRequest handle it
        $this->client = null;
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
            $statusCode = null;
            $body = null;

            // Priority 1: If an injected Laravel HTTP client (Illuminate\Http\Client\Factory) is set, use it
            if (is_object($this->client) && class_exists('\Illuminate\Http\Client\Factory') && $this->client instanceof \Illuminate\Http\Client\Factory) {
                $laravelOptions = ['timeout' => $this->timeout];

                if ($method === 'GET') {
                    $res = $this->client->withOptions($laravelOptions)->get($fullUrl, $data);
                } else {
                    if ($bodyFormat === 'json') {
                        $res = $this->client->withOptions($laravelOptions)->post($fullUrl, $data);
                    } else {
                        $res = $this->client->asForm()->withOptions($laravelOptions)->post($fullUrl, $data);
                    }
                }

                $statusCode = $res->status();
                $body = $res->body();
            }
            // Priority 2: If client is null but Http facade exists, use the facade
            elseif ($this->client === null && class_exists('Illuminate\\Support\\Facades\\Http')) {
                $laravelOptions = ['timeout' => $this->timeout];
                $res = null;

                try {
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
                } catch (\Exception $e) {
                    throw new SimbaApiException(
                        'Laravel HTTP Client error: ' . $e->getMessage(),
                        500
                    );
                }
            }
            // Priority 3: Fallback to CodeIgniter client (Guzzle-like)
            else {
                // If we still don't have a client and Laravel Http not available, try to use PHP streams or throw error
                if (!is_object($this->client)) {
                    // Last resort: try Laravel Http via use statement if we're in Laravel environment
                    if (class_exists('Illuminate\\Support\\Facades\\Http')) {
                        try {
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
                            // Successfully used Laravel Http, break to processing
                            if ($statusCode !== null && $body !== null) {
                                // Continue to response processing below
                            }
                        } catch (\Exception $e) {
                            throw new SimbaApiException(
                                'HTTP Client tidak tersedia. Error: ' . $e->getMessage(),
                                500
                            );
                        }
                    } else {
                        throw new SimbaApiException(
                            'HTTP Client tidak tersedia. Pastikan Laravel Http atau CodeIgniter Services::curlrequest() dapat diakses.',
                            500
                        );
                    }
                } elseif (!method_exists($this->client, 'request')) {
                    throw new SimbaApiException(
                        'HTTP Client tidak memiliki method request().',
                        500
                    );
                } else {
                    // Build full URL for GET requests
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

                    // Use CodeIgniter's curl request client
                    $response = $this->client->request($method, $fullUrl, $options);

                    $statusCode = $response->getStatusCode();
                    $body = (string)$response->getBody();
                }
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
