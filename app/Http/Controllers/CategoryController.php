<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get list of categories with child
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories(){
        $list = Category::query()->with('child_category')->get();
        return response()->json(['a' => $list]);
    }
}
