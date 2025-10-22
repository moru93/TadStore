<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException as EloquentModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Auth\Access\AuthorizationException as AuthzException;
use Illuminate\Auth\AuthenticationException as AuthnException;

use Throwable;

class Handler extends ExceptionHandler
{

    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registro principal de los manejadores de excepciones.
     */
    public function register()
    {
        // Manejo de errores tipo ModelNotFound
        $this->renderable(function (Throwable $e, $request) {
            if ($e instanceof EloquentModelNotFoundException || $e instanceof NotFoundHttpException) {
                return $this->handleModelNotFound($e, $request);
            }
        });

        // Manejo de errores de validación (Laravel)
        $this->renderable(function (LaravelValidationException $e, $request) {
            return $this->handleValidation($e, $request);
        });

        // Manejo de errores de autorización
        $this->renderable(function (UnauthorizedHttpException $e, $request) {
            return $this->handleUnauthorized($e, $request);
        });
        
        $this->renderable(function (AuthzException $e, $request) {
            return $this->handleUnauthorized($e, $request); // tu handleUnauthorized ya devuelve 403
        });

        // Errores de negocio (custom)
        $this->renderable(function (\Exception $e, $request) {
            return $this->handleBusinessExceptions($e, $request);
        });

        // Manejadores personalizados (creados como funciones locales)
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return $this->handleGeneric($e, $request);
            }
        });
        
        // Manejo de AuthenticationException (no autenticado)
        $this->renderable(function (AuthnException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e,
                ], 401);
            }

            return redirect()->guest(route('login'));
        });
    }

    /**
     * Maneja errores de modelo no encontrado.
     */
    protected function handleModelNotFound(Throwable $e, $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'El recurso solicitado no existe o no fue encontrado.',
            ], 404);
        }

        return response()->view('errors.404', ['message' => 'Recurso no encontrado.'], 404);
    }

    /**
     * Maneja errores de validación (Request::validate o FormRequest).
     */
    protected function handleValidation(LaravelValidationException $e, $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Los datos enviados no son válidos.',
                'errors' => $e->errors(),
            ], 422);
        }

        return redirect()->back()->withErrors($e->errors())->withInput();
    }

    /**
     * Maneja errores de autorización (middleware o policies).
     */
    protected function handleUnauthorized(Throwable $e, $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permisos para realizar esta acción.',
            ], 403);
        }

        return response()->view('errors.403', ['message' => 'Acceso no autorizado.'], 403);
    }

    /**
     * Maneja errores generales no controlados (fallback).
     */
    protected function handleGeneric(Throwable $e, $request)
    {
        $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        return response()->json([
            'status' => 'error',
            'message' => $status === 500
                ? 'Error interno del servidor. Por favor, intente más tarde.'
                : $e->getMessage(),
            'debug' => config('app.debug') ? [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5),
            ] : null,
        ], $status);
    }

    /**
     * Función adicional opcional: lanzar una excepción de validación personalizada.
     * throw $this->validationException(['email' => ['El correo ya está en uso.']]);
     */
    public function validationException(array $errors, string $message = 'Error de validación')
    {
        return new LaravelValidationException(
            validator([], []), // Validator vacío solo para compatibilidad
            response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $errors,
            ], 422)
        );
    }

    public function invalidCredentialsException(string $message = 'Credenciales inválidas')
    {
        return new \Exception($message, 401);
    }

    /* -----------------------------------------
     |  HANDLERS DE NEGOCIO PERSONALIZADOS
     ----------------------------------------- */

    protected function handleBusinessExceptions(\Exception $e, $request)
    {
        // Mapeo de códigos de error a mensajes estándar
        $map = [
            // Categorías
            4091 => 'No se pudo eliminar la categoría. Tiene productos asociados.',
            4092 => 'No se pudo actualizar la categoría. Error de datos o duplicidad.',
            4093 => 'No se pudo crear la categoría. Verifica los datos enviados.',

            // Productos
            4094 => 'No se pudo eliminar el producto. Está vinculado a un pedido.',
            4095 => 'No se pudo completar la operación de compra.',
            4096 => 'No se pudo crear el producto. Verifica los datos o la imagen.',
            4097 => 'No se pudo actualizar el producto. Error al guardar datos o imagen.',
            4098 => 'No se pudo eliminar el producto. Error al borrar la imagen o el registro.',

            4099 => 'No se pudo completar la operación de compra.',
            4080 => 'Error al enviar el correo de verificación.',
            4081 => 'Código de verificación inválido o expirado.',
            4082 => 'Producto no encontrado.',
            4083 => 'Stock insuficiente para completar el pedido.',
        ];

        if (isset($map[$e->getCode()])) {
            return response()->json([
                'status' => 'error',
                'message' => $map[$e->getCode()],
            ], 409);
        }

        return null; // Si no está en el mapa, deja que otro renderable lo maneje
    }

    /* -----------------------------------------
     |  FACTORÍAS DE EXCEPCIONES
     ----------------------------------------- */
    // Categorías
    public static function cannotDeleteCategory()
    {
        throw new \Exception('', 4091);
    }

    public static function cannotUpdateCategory()
    {
        throw new \Exception('', 4092);
    }

    public static function cannotCreateCategory()
    {
        throw new \Exception('', 4093);
    }
        
    // Productos
    public static function cannotDeleteProduct()
    {
        throw new \Exception('', 4098);
    }

    public static function cannotUpdateProduct()
    {
        throw new \Exception('', 4097);
    }

    public static function cannotCreateProduct()
    {
        throw new \Exception('', 4096);
    }

    public static function purchaseOperationFailed()
    {
        throw new \Exception('', 4095);
    }

    // Órdenes
    public static function cannotSendVerificationCode()
    {
        throw new \Exception('', 4099);
    }

    public static function invalidVerificationCode()
    {
        throw new \Exception('', 4080);
    }

    public static function productNotFound()
    {
        throw new \Exception('', 4081);
    }

    public static function insufficientStock(string $productName)
    {
        throw new \Exception("Stock insuficiente para $productName", 4082);
    }

    public static function cannotCompleteOrder()
    {
        throw new \Exception('', 4083);
    }
}
