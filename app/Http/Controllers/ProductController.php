<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = ProductResource::collection(Product::latest()->get());
        if (is_null($products->first())) {
            return $this->errorResponse('No product found!', 404);
        }
        return $this->successResponse($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation Error!', 422, $validator->errors());
        }

        $product = Product::create($request->all());

        return $this->successResponse(new ProductResource($product), 'Product is created', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (! $product) {
            return $this->errorResponse('Product not found', 404);
        }

        return $this->successResponse(new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation Error!', 422, $validator->errors());
        }

        $product = Product::find($id);

        if (! $product) {
            return $this->errorResponse('Product not found', 404);
        }

        $product->update($request->all());

        return $this->successResponse(new ProductResource($product), 'Product is updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (! $product) {
            return $this->errorResponse('Product not found', 404);
        }

        $product->delete();

        return $this->successResponse(null, 'Product is deleted');
    }
}