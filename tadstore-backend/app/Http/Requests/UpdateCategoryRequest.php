<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Reglas de validación para actualizar una categoría.
     */
    public function rules()
    {
        // Obtenemos el ID actual de la categoría desde la ruta
        $categoryId = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name'        => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Sanitización de los campos antes de la validación.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name'        => $this->has('name') ? trim(strip_tags($this->name)) : null,
            'description' => $this->has('description') ? trim(strip_tags($this->description)) : null,
        ]);
    }

    /**
     * Mensajes personalizados de error para mejorar la UX.
     */
    public function messages()
    {
        return [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.string'   => 'El nombre debe ser una cadena de texto válida.',
            'name.max'      => 'El nombre no puede superar los 255 caracteres.',
            'name.unique'   => 'Ya existe una categoría con ese nombre.',

            'description.string' => 'La descripción debe ser texto válido.',
            'description.max'    => 'La descripción no puede tener más de 1000 caracteres.',
        ];
    }
}
