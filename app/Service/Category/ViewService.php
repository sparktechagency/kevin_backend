<?php

namespace App\Service\Category;

use App\Models\Category;
use App\Traits\ResponseHelper;

class ViewService
{
  use ResponseHelper;

   public function view($id)
    {
        $category = Category::find($id);
        if(!$category){
            return $this->errorResponse("Category not found.");
        }
        return $this->successResponse($category, 'Category view successfully.');
    }
}
