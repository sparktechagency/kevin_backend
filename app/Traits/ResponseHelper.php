<?php

namespace App\Traits;

use Exception;

trait ResponseHelper
{
    public function successResponse($data, $message = 'Request successful', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }
   public function errorResponse($message, $status = 400, $errors = null)
    {
        $response = ['message' => $message];

        if($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    protected function execute(callable $callback)
    {
        try {
            return $callback();
        } catch (Exception $e) {
            return $this->errorResponse("Something went wrong. Please try again later. " . $e->getMessage());
        }
    }
}
