<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Reglas de validación que se aplican al request.
     */
    public function rules()
    {
        $id = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name' => 'required|string|max:255|unique:categories,name' . ($id ? ",$id" : ''),
            'description' => 'nullable|string',
        ];
    }

    /**
     * Limpia los datos antes de validar.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            // Elimina etiquetas HTML y espacios innecesarios
            'name' => $this->has('name') ? trim(strip_tags($this->name)) : null,
            'description' => $this->has('description')
                ? trim(strip_tags($this->description))
                : null,
        ]);
    }

    /**
     * Mensajes personalizados
     */
    public function messages()
    {
        return [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.unique' => 'Ya existe una categoría con este nombre.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
        ];
    }
}
