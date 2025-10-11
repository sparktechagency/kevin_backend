<?php

namespace App\Service\Auth;

use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Request;

class CheckTokenService
{
   use ResponseHelper;
    public function checkToken($request)
    {
        if ($request->user()) {
            return response()->json([
                'success' => true,
                'message' => 'Token is valid.',
                'data' => [
                    'valid' => true
                ]
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token is invalid or expired.',
            'data' => [
                'valid' => false
            ]
        ], 401); 
    }
}
