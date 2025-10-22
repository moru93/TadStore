<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Reglas de validación aplicadas al inicio de sesión.
     */
    public function rules()
    {
        return [
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6|max:100',
        ];
    }

    /**
     * Limpieza (sanitización) de los datos antes de validar.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'email' => $this->has('email') ? strtolower(trim($this->email)) : null,
            'password' => $this->has('password') ? trim($this->password) : null,
        ]);
    }

    /**
     * Mensajes personalizados para errores de validación.
     */
    public function messages()
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede exceder los 150 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto válida.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.max' => 'La contraseña no puede superar los 100 caracteres.',
        ];
    }
}
