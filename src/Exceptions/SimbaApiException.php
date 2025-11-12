<?php

namespace simba\api\Exceptions;

use Exception;

/**
 * Exception untuk API Simba
 * 
 * Digunakan untuk error yang berhubungan dengan API Simba
 */
class SimbaApiException extends Exception
{
    protected $statusCode;
    protected $errorData;

    public function __construct(
        string $message = "",
        int $statusCode = 0,
        array $errorData = [],
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->statusCode = $statusCode;
        $this->errorData = $errorData;
    }

    /**
     * Get HTTP Status Code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get Error Data
     */
    public function getErrorData(): array
    {
        return $this->errorData;
    }

    /**
     * Convert to Array
     */
    public function toArray(): array
    {
        return [
            'success' => false,
            'status_code' => $this->statusCode,
            'message' => $this->message,
            'data' => $this->errorData
        ];
    }
}
