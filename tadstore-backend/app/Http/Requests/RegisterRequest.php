<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Reglas de validación que aplican al registro de usuario.
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:13',
                'confirmed', // Debe existir un campo password_confirmation
                'regex:/[A-Z]/', // Al menos una mayúscula
                'regex:/[a-z]/', // Al menos una minúscula
                'regex:/[0-9]/', // Al menos un número
                'regex:/[@$!%*#?&]/', // Al menos un carácter especial
            ],
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
            'password' => $this->has('password') ? trim($this->password) : null,
        ]);
    }

    /**
     * Mensajes personalizados para mejorar la experiencia del usuario.
     */
    public function messages()
    {
        return [
            // Nombre
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',

            // Correo electrónico
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'Ya existe una cuenta registrada con este correo.',
            'email.max' => 'El correo electrónico no puede tener más de 150 caracteres.',

            // Contraseña
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto válida.',
            'password.min' => 'La contraseña debe tener al menos 13 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex' => 'La contraseña debe incluir al menos una mayúscula, una minúscula, un número y un carácter especial (@, $, !, %, *, #, ?, &).',
        ];
    }
}
