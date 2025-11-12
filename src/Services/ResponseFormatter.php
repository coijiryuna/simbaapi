<?php

namespace simba\api\Services;

/**
 * Response Formatter Service
 * 
 * Untuk membuat format response yang konsisten
 */
class ResponseFormatter
{
    /**
     * Success Response
     * 
     * @param mixed $data Data yang akan dikembalikan
     * @param string $message Pesan sukses
     * @param int $statusCode HTTP Status Code
     * @return array
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200): array
    {
        return [
            'success' => true,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Error Response
     * 
     * @param string $message Pesan error
     * @param int $statusCode HTTP Status Code
     * @param array $errors Daftar error detail
     * @return array
     */
    public static function error(string $message = 'Error', int $statusCode = 400, array $errors = []): array
    {
        return [
            'success' => false,
            'status_code' => $statusCode,
            'message' => $message,
            'errors' => $errors
        ];
    }

    /**
     * Paginated Response
     * 
     * @param array $data Data items
     * @param int $total Total items
     * @param int $perPage Items per page
     * @param int $currentPage Current page
     * @param string $message Pesan
     * @return array
     */
    public static function paginated(array $data, int $total, int $perPage = 10, int $currentPage = 1, string $message = 'Success'): array
    {
        $totalPages = ceil($total / $perPage);

        return [
            'success' => true,
            'status_code' => 200,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'last_page' => $totalPages,
                'from' => (($currentPage - 1) * $perPage) + 1,
                'to' => min($currentPage * $perPage, $total)
            ]
        ];
    }

    /**
     * Validation Error Response
     * 
     * @param array $errors Validation errors
     * @return array
     */
    public static function validationError(array $errors): array
    {
        return self::error('Validation failed', 422, $errors);
    }
}
