<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Reglas de validación que aplican al request.
     */
    public function rules()
    {
        return [
            // Datos del comprador
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:255',

            // Ítems del pedido
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Limpieza (sanitización) de los datos antes de validar.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => $this->has('name') ? trim(strip_tags($this->name)) : null,
            'email' => $this->has('email') ? strtolower(trim($this->email)) : null,
            'phone' => $this->has('phone') ? preg_replace('/[^0-9+]/', '', $this->phone) : null, // Solo números y "+"
            'address' => $this->has('address') ? trim(strip_tags($this->address)) : null,
        ]);
    }

    /**
     * Mensajes personalizados para mejorar la experiencia de usuario.
     */
    public function messages()
    {
        return [
            // Datos del comprador
            'name.required' => 'El nombre del comprador es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede tener más de 150 caracteres.',

            'phone.required' => 'El número de teléfono es obligatorio.',
            'phone.string' => 'El número de teléfono debe ser una cadena válida.',
            'phone.max' => 'El número de teléfono no puede superar los 30 caracteres.',

            'address.required' => 'La dirección de envío es obligatoria.',
            'address.string' => 'La dirección debe ser texto plano.',
            'address.max' => 'La dirección no puede superar los 255 caracteres.',

            // Ítems del pedido
            'items.required' => 'Debe incluir al menos un producto en el pedido.',
            'items.array' => 'El formato de los productos enviados no es válido.',
            'items.min' => 'Debe haber al menos un producto en el carrito.',

            'items.*.product_id.required' => 'Cada producto debe tener un identificador.',
            'items.*.product_id.integer' => 'El identificador del producto debe ser un número.',
            'items.*.product_id.exists' => 'Uno o más productos seleccionados no existen.',

            'items.*.quantity.required' => 'Debe especificar la cantidad de cada producto.',
            'items.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'items.*.quantity.min' => 'La cantidad mínima de cada producto es 1.',
        ];
    }
}
