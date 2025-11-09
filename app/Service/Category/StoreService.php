<?php

namespace App\Service\Category;

use App\Models\Category;
use App\Traits\ResponseHelper;

class StoreService
{
   use ResponseHelper;

   public function store($data)
    {
        if (isset($data['icon'])) {
            $logoPath = $data['icon']->store('category_icons', 'public');
            $data['icon'] = 'storage/' . $logoPath;
        }
        $category = Category::create($data);
        return $this->successResponse($category, 'Category created successfully.');
    }
}
