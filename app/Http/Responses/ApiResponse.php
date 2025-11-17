<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;

class ApiResponse
{
    public static function success($data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public static function error(string $message, $errors = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
            'data'    => null,
        ], $status);
    }

 
    public static function paginated(AbstractPaginator $paginator, string $message = ''): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ]
        ]);
    }
}
