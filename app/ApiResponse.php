<?php

namespace App;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        $response = ['status' => true];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    protected function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $status);
    }

    protected function searchResult(array $data, string $query, string $field): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $data,
            'meta' => [
                'total' => count($data),
                'query' => $query,
                'field' => $field,
            ],
        ]);
    }
}
