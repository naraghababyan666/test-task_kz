<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public $user = null;
    protected $ip = null;
    public function __construct(Request $request)
    {
        $token = $request->bearerToken();
        if (!empty($token)) {
            $this->user = auth('sanctum')->user();
        }
        $http = \Illuminate\Support\Facades\Http::get('https://api64.ipify.org?format=json');
        $this->ip = json_decode($http->body())->ip;
    }
    /**
     * Buy product
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function  buyProduct(Request $request, $id)
    {
        $product = Product::query()->find($id);
        $orderInfo = Validator::make($request->all(), [
           'count' => 'required|numeric'
        ]);
        if($orderInfo->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Insert amount and product count',
                'fields' => $orderInfo->errors()
            ]);
        }
        if(!is_null($product)) {
            $order = new Order();
            $order->product_id = $id;
            if(!is_null($this->user)){
                $order->user_id = $this->user['id'];
                $order->customer_name = $this->user['name'];
                $order->email = $this->user['email'];
                $order->phone = $this->user['phone'];
                $order->count = $orderInfo->validated()['count'];
                $order->amount = 0;
                $order->save();
            }else{
                $userInfo = Validator::make($request->all(), [
                    'name' => 'required|max:50',
                    'email' => 'required|email',
                    'phone' => 'required',
                ]);
                if($userInfo->fails()){
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid user info',
                        'fields' => $userInfo->errors()
                    ]);
                }
                $order->user_id = $this->ip;
                $order->customer_name = $userInfo->validated()['name'];
                $order->email = $userInfo->validated()['email'];
                $order->phone = $userInfo->validated()['phone'];
                $order->count = $orderInfo->validated()['count'];
                $order->amount = 0;
                $order->save();
            }
            return response()->json(['success' => true, 'message' => 'Shopping completed!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }
    }

    public function getMyOrderList(){
       $orders = Order::query()
           ->where('user_id', Auth::id())
           ->select('id', 'product_id', 'count')
           ->with(['product' => function($q){
               $q->select('id', 'name', 'slug', 'price');
           }])
           ->get();
        if(count($orders) == 0){
            return response()->json(['success' => true, 'message' => 'Empty list']);
        }
        return response()->json(['success' => true, 'data' => $orders]);

    }
}
