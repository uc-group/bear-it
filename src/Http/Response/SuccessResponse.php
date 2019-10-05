<?php

namespace App\Http\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct([
            'status' => 'OK',
            'data' => $data
        ], $status, $headers, $json);
    }
}
