<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     * Send a success response.
     */
    public function sendResponse($result, $message): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ], 200);
    }

    /**
     * Send an error response.
     */
    public function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
