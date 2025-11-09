<?php

namespace App\Service\Category;

use App\Models\Category;
use App\Traits\ResponseHelper;
use Illuminate\Support\Facades\Storage;

class DeleteService
{
    use ResponseHelper;
    public function delete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->errorResponse('Category not found.');
        }
        if ($category->icon && Storage::disk('public')->exists($category->icon)) {
            Storage::disk('public')->delete($category->icon);
        }
        $category->delete();
        return $this->successResponse([], 'Category deleted successfully.');
    }
}
