<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validated = Validator::make($data, [
            'product_id' => 'required'
        ]);
        if($validated->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Insert product id'
            ]);
        }

        if(!is_null(Product::find($validated->validated()['product_id']))){
            $newCart = new Cart();
            $newCart->product_id = $validated->validated()['product_id'];
            if(is_null($this->user)){
                $order = Cart::query()->where('product_id', $validated->validated()['product_id'])
                    ->where('user_id', $this->ip)->first();
                if(!is_null($order)){
                    return response()->json(['success' => false, 'message' => 'Product already in cart list']);
                }
                $newCart->user_id = $this->ip;
                $newCart->save();
            }
            else{
                $order = Cart::query()->where('product_id', $validated->validated()['product_id'])
                    ->where('user_id', $this->user['id'])->first();
                if(!is_null($order)){
                    return response()->json(['success' => false, 'message' => 'Product already in cart list']);
                }
                $newCart->user_id = $this->user['id'];
                $newCart->save();
            }
            return response()->json(['success' => true, 'data' => $newCart]);
        }
        return response()->json(['success' => false, 'message' => 'Product not found']);
    }

    /**
     * Remove the specified product from cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'product_id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
        }
        $item = Cart::query()->where('product_id', $data['product_id']);
        if(!is_null($this->user)){
            $item->where('user_id',$this->user['id'])->first();
        }else{
            $item->where('user_id',$this->ip)->first();

        }
        $item =$item->first();
        if(!is_null($item)){
            $item->delete();
            return response()->json(['success' => true, 'message' => 'Product successfully deleted from cart!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
    }

    /**
     * Remove all products from my cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroyAll(Request $request){
        $data = $request->all();
        $query = Cart::query();
        if(!is_null($this->user)){
            $cartItems = $query->where('user_id', $this->user['id'])->get();
        }else{
            $cartItems = $query->where('user_id', $this->ip)->get();
        }
        if(count($cartItems) == 0) {
            return response()->json(['success' => false, 'message' => 'Empty cart list']);
        }
        $query->delete();
        return response()->json(['success' => true, 'message' => 'Products successfully removed from cart list']);
    }
}
