<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseJson
{
    /**
     * @param int $status
     * @param bool $success
     * @param string $message
     * @param Object|null $data
     * @return JsonResponse
     */
    public function data(int $status, bool $success, string $message, ?Object $data = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "status" => $status,
            "success" => $success,
            "message" => $message,
            "data" => $data ? $data : [],
        ]);
    }
}
