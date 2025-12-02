<?php

namespace App\Service\Auth;

use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Auth;

class DeleteService
{
    use ResponseHelper;

    public function delete()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->errorResponse('User not authenticated');
        }

        // delete account
        $user->delete();

        // revoke token (Sanctum)
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        return $this->successResponse([], 'Your account has been deleted successfully.');
    }
}
