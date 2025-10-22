<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Exceptions\Handler;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware('admin')->only(['store', 'update', 'destroy']);
    }
    
    public function index(Request $request)
    {
        //
        $query = Product::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        $perPage = $request->get('per_page', 8);
        return response()->json($query->paginate($perPage));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        try {
            if ($request->hasFile('image_url')) {
                $path = $request->file('image_url')->store('products', 'public');
                $data['image_url'] = '/storage/' . $path;
            }

            $product = Product::create($data);
            if (!$product) {
                Handler::cannotCreateProduct();
            }

            return response()->json($product, 201);
        } catch (\Throwable $e) {
            Handler::cannotCreateProduct();
        }
    }

    public function show(Product $product)
    {
        //
        return response()->json($product->load('category'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        try {
            if ($request->hasFile('image_url')) {
                // eliminar imagen anterior si existe
                if ($product->image_url && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image_url))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $product->image_url));
                }

                $path = $request->file('image_url')->store('products', 'public');
                $data['image_url'] = "/storage/" . $path;
            }

            if (!$product->update($data)) {
                Handler::cannotUpdateProduct();
            }

            return response()->json($product, 200);
        } catch (\Throwable $e) {
            Handler::cannotUpdateProduct();
        }
    }

    public function destroy(ProductRequest $product)
    {
        try {
            if ($product->image_url) {
                $oldPath = str_replace('/storage/', '', $product->image_url);

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            if (!$product->delete()) {
                Handler::cannotDeleteProduct();
            }

            return response()->json(['message' => 'Producto eliminado'], 204);
        } catch (\Throwable $e) {
            Handler::cannotDeleteProduct();
        }
    }
}

