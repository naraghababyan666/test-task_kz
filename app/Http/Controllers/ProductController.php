<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts(Request $request)
    {
        $data = $request->all();
        $where = [];
        if(isset($data['name'])){
            $where[] = "prod.name LIKE '%".$data['name']."%'";
        }
        if(isset($data['description'])){
            $where[] = "prod.description LIKE '%".$data['description']."%'";
        }
        if(isset($data['categories'])){
            $where[] = "prod.category_id IN (".$data['categories'].")";
        }
        if(isset($data['price'])){
            $where[] = "prod.price = (".$data['price'].")";
        }
        $where_text = '';
        if(!empty($where)){
            $where_text = 'WHERE ' . implode(" AND ", $where);
        }
        $sql = "SELECT prod.id as product_id, prod.category_id, prod.name, prod.description, prod.slug,
                       cat.name as category_name, prod.price,
                       CONCAT(prod.width,' X ', prod.height, ' X ', prod.weight) as product_size
                FROM `products` as prod
                INNER JOIN `categories` as cat ON cat.id = prod.category_id
                {$where_text}
                ;";
        $data = DB::select($sql);
        if(count($data) == 0){
            return response()->json(['success' => true, 'message' => 'Empty list']);
        }
        return response()->json(['success' => true, 'data' => $data]);
    }
    /**
     * Getting product by slug
     * @param $slug
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductBySlug($slug){
        if(!isset($slug)){
            return response()->json(['success' => false, 'message' => 'Empty slug field']);
        }
        $products = Product::query()->where('slug', 'LIKE', '%'.$slug.'%')->get();
        if(count($products) == 0){
            return response()->json(['success' => true, 'message' => 'Not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $products]);
    }


}
