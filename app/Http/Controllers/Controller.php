<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base controller aplikasi.
 *
 * Menyediakan trait standar Laravel dan helper response JSON.
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Response sukses standar.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $status
     * @return JsonResponse
     */
    protected function respondSuccess($data = null, string $message = null, int $status = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    /**
     * Response error standar.
     *
     * @param string|null $message
     * @param int $status
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function respondError(string $message = null, int $status = 400, $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message ?? 'Terjadi kesalahan',
        ];

        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }

    /**
     * Response created (201).
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function respondCreated($data = null, string $message = 'Berhasil dibuat'): JsonResponse
    {
        return $this->respondSuccess($data, $message, 201);
    }

    /**
     * Response no content (204).
     *
     * @return JsonResponse
     */
    protected function respondNoContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    /**
     * Response untuk pagination.
     *
     * @param LengthAwarePaginator $paginator
     * @param string $dataKey
     * @param string|null $message
     * @param int $status
     * @return JsonResponse
     */
    protected function respondPaginated(LengthAwarePaginator $paginator, string $dataKey = 'data', string $message = null, int $status = 200): JsonResponse
    {
        $payload = [
            'success' => true,
            'message' => $message,
            $dataKey => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
        ];

        return response()->json($payload, $status);
    }

    /**
     * Wrapper validation.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return array
     */
    protected function validateInput($request, array $rules, array $messages = [], array $customAttributes = []): array
    {
        return $this->validate($request, $rules, $messages, $customAttributes);
    }
}
