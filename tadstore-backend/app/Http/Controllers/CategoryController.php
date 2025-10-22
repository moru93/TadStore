<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Exceptions\Handler;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware('admin')->only(['store', 'update', 'destroy']);
    }

    /**
     * 📄 Listar categorías con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 10);

        return response()->json($query->paginate($perPage));
    }

    /**
     * 🆕 Crear categoría.
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        try {
            $data['slug'] = Str::slug($data['name']);
            $category = Category::create($data);
        } catch (\Throwable $e) {
            Handler::cannotCreateCategory();
        }

        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'category' => $category,
        ], 201);
    }

    /**
     * 👁️ Mostrar categoría específica.
     */
    public function show(Category $category)
    {
        return response()->json([
            'category' => $category->load('products'),
        ]);
    }

    /**
     * ✏️ Actualizar categoría.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (!$category->update($data)) {
            Handler::cannotUpdateCategory();
        }

        return response()->json([
            'message' => 'Categoría actualizada correctamente',
            'category' => $category,
        ], 200);
    }

    /**
     * 🗑️ Eliminar categoría.
     */
    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            Handler::cannotDeleteCategory();
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente',
        ], 200);
    }
}
