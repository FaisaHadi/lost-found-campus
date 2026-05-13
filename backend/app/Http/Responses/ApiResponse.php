<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait ApiResponse
{
    protected function successResponse(
        mixed $data = [],
        string $message = 'Permintaan berhasil diproses.',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'meta' => [],
        ], $status);
    }

    protected function paginatedResponse(
        AnonymousResourceCollection $resourceCollection,
        string $message = 'Data berhasil diambil.'
    ): JsonResponse {
        $response = $resourceCollection->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $response['data'] ?? [],
            'errors' => null,
            'meta' => $response['meta'] ?? [],
            'links' => $response['links'] ?? [],
        ]);
    }

    protected function errorResponse(
        string $code,
        string $message,
        int $status = 400,
        mixed $details = null
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $details,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'meta' => [],
        ];

        if ($details !== null) {
            $payload['error']['details'] = $details;
        }

        return response()->json([
            ...$payload,
        ], $status);
    }
}
