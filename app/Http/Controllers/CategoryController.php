<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_visible', true)
            ->with(['children' => function ($query) {
                $query->where('is_visible', true)
                    ->with(['children' => function ($query) {
                        $query->where('is_visible', true);
                    }]);
            }])
            ->get();

        return CategoryResource::collection($categories);
    }
}
