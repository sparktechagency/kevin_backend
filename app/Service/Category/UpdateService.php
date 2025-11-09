<?php

namespace App\Service\Category;

use App\Models\Category;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Storage;

class UpdateService
{
  use ResponseHelper;

    public function update($data, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->errorResponse('Category not found.', 404);
        }
        if (isset($data['icon'])) {
            if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                Storage::disk('public')->delete($category->icon);
            }
            $logoPath = $data['icon']->store('category_icons', 'public');
            $data['icon'] = 'storage/' . $logoPath;
        }
        $category->update($data);
        return $this->successResponse($category, 'Category updated successfully.');
    }
}
