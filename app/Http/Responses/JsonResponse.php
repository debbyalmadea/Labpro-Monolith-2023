<?php

namespace App\Http\Responses;

use App\Enums\HttpStatusCodes;
use Illuminate\Http\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse
{
    protected $message;
    public function __construct($status, $data = null, $message = null, $status_code = HttpStatusCodes::OK, array $headers = [])
    {
        $response = [
            'status' => $status,
            'data' => $data,
            'message' => $message,
        ];

        parent::__construct($response, $status_code, $headers);
    }

    public static function success($data = null, $message = null, $status_code = HttpStatusCodes::OK, array $headers = [])
    {
        return new static('success', $data, $message, $status_code, $headers);
    }

    public static function error($message = null, $status_code = HttpStatusCodes::BAD_REQUEST, array $headers = [])
    {
        return new static('error', null, $message, $status_code, $headers);
    }
}