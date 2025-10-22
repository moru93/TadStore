<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ProductRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Reglas de validación del producto.
     */
    public function rules()
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image_url'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Sanitiza los datos antes de validarlos.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => $this->has('name') ? trim(strip_tags($this->name)) : null,
            'description' => $this->has('description')
                ? trim(strip_tags($this->description))
                : null,
            'price' => $this->has('price') ? floatval(preg_replace('/[^\d.]/', '', $this->price)) : 0,
            'stock' => $this->has('stock') ? intval($this->stock) : 0,
        ]);
    }

    /**
     * Mensajes personalizados de error.
     */
    public function messages()
    {
        return [
            // 🔹 Campos base
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists'   => 'La categoría seleccionada no existe.',
            'category_id.integer'  => 'El ID de categoría debe ser un número válido.',

            'name.required'        => 'El nombre del producto es obligatorio.',
            'name.string'          => 'El nombre del producto debe ser texto.',
            'name.max'             => 'El nombre no puede superar los 255 caracteres.',

            'description.string'   => 'La descripción debe ser texto válido.',
            'description.max'      => 'La descripción no puede superar los 2000 caracteres.',

            'price.required'       => 'El precio es obligatorio.',
            'price.numeric'        => 'El precio debe ser un número válido.',
            'price.min'            => 'El precio no puede ser negativo.',

            'stock.required'       => 'El stock es obligatorio.',
            'stock.integer'        => 'El stock debe ser un número entero.',
            'stock.min'            => 'El stock no puede ser negativo.',

            // 🔹 Imagen
            'image_url.image'      => 'El archivo debe ser una imagen válida.',
            'image_url.mimes'      => 'Solo se permiten imágenes en formato JPG, JPEG o PNG.',
            'image_url.max'        => 'La imagen no puede superar los 2 MB.',
        ];
    }
}
