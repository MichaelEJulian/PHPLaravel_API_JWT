<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use JWTAuth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }   

    public function index()
    {
        return $this->user
            ->products()
            ->get(['name', 'price', 'quantity'])
            ->toArray();
    }

    public function show($id)
    {
        $product = $this->user->products()->find($id);
    
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product with id ' . $id . ' cannot be found'
            ], 400);
        }
    
        return $product;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric|between:0,9999.99',
            'quantity' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'Failed' => true,
                'data' => "Error encountered"
            ], 500);
        }
    
        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
    
        if ($this->user->products()->save($product))
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product could not be added'
            ], 500);
    }

    public function update(Request $request, $id)
    {
        $product = $this->user->products()->find($id);
    
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product with id ' . $id . ' cannot be found'
            ], 400);
        }
    
        $updated = $product->fill($request->all())
            ->save();
    
        if ($updated) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product could not be updated'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $product = $this->user->products()->find($id);
    
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product with id ' . $id . ' cannot be found'
            ], 400);
        }
    
        if ($product->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product could not be deleted'
            ], 500);
        }
    }
}
