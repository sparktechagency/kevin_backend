<?php

namespace App\Service\Post;

use App\Models\Category;
use App\Traits\ResponseHelper;

class SearchTopicesService
{
   use ResponseHelper;
    public function searchTopices($request)
    {
        $search = $request->search; // search keyword

        $categories = Category::with(['dreams'])
            ->withCount('dreams')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('dreams_count')
            ->get();

        return $this->successResponse($categories, "Categories with dreams and count retrieved successfully.");
    }


}
