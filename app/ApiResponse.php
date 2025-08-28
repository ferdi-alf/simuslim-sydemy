<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * Success response
     */
    public function successResponse($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Error response
     */
    public function errorResponse($message = 'Error', $code = 400, $errors = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ], $code);
    }

    /**
     * Unauthorized response
     */
    public function unauthorizedResponse($message = 'Unauthorized')
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null
        ], 401);
    }

    /**
     * Not found response
     */
    public function notFoundResponse($message = 'Data not found')
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null
        ], 404);
    }

    /**
     * Validation error response
     */
    public function validationErrorResponse($errors, $message = 'Validation failed')
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'errors' => $errors
        ], 422);
    }
}