<?php

namespace App\Service\ManagerDashboard;

use App\Models\Category;
use App\Traits\ResponseHelper;

class AnalyticsService
{
    use ResponseHelper;

    public function analytics($request)
    {
        $data = Category::withCount('dreams')->get();
        return $this->successResponse($data, 'Analytics data');
    }
}
