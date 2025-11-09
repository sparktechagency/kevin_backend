<?php

namespace App\Service\Category;

use App\Models\Category;
use App\Traits\ResponseHelper;

class IndexService
{
    use ResponseHelper;
    public function index($request)
    {
        $query = Category::query();
        $categories = $query->orderBy('created_at', 'desc')
                        ->paginate($request->per_page ?? 10);
        return $this->successResponse($categories, 'Categories fetched successfully.');
    }
}
