<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);
        return response()->json([
            "message" => "products",
            "results" => $products
        ], 200);
    }

    public function show($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                "message" => "Product not found"
            ], 404);
        }
        
        return response()->json([
            "message" => "product detail",
            "results" => $product
        ], 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category' => 'required|max:50',
            'sku' => 'required|max:50|unique:products',
            'is_active' => 'boolean'
        ]);

        $product = Product::create($request->all());
        
        return response()->json([
            "message" => "Product created successfully",
            "results" => $product
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                "message" => "Product not found"
            ], 404);
        }

        $this->validate($request, [
            'name' => 'max:100',
            'price' => 'numeric',
            'stock' => 'integer',
            'category' => 'max:50',
            'sku' => 'max:50|unique:products,sku,'.$id,
            'is_active' => 'boolean'
        ]);

        $product->update($request->all());
        
        return response()->json([
            "message" => "Product updated successfully",
            "results" => $product
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                "message" => "Product not found"
            ], 404);
        }

        $product->delete();
        
        return response()->json([
            "message" => "Product deleted successfully"
        ], 200);
    }
}
