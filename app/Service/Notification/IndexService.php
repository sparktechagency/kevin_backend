<?php

namespace App\Service\Notification;

use App\Models\Notify;
use App\Traits\ResponseHelper;

class IndexService
{
    use ResponseHelper;

    public function index($request)
    {
        $query = Notify::query();
        $perPage = $request->per_page ?? 10;

        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->successResponse($notifications, 'Notifications retrieved successfully.');
    }
}
