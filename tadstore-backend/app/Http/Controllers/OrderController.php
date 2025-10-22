<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\OrderVerification;
use App\Mail\OrderVerificationMail;
use App\Mail\OrderConfirmedMail;
use App\Http\Requests\OrderRequest;


use App\Exceptions\Handler;

class OrderController extends Controller
{
    /**
     * Paso 1: Enviar código de verificación.
     */
    public function sendCode(OrderRequest $request)
    {
        try {

            $validated = $request->validate([
                'name' => 'required|string|max:150',
                'email' => 'required|email|max:150',
            ]);

            $code = random_int(100000, 999999);

            // Eliminar códigos previos del mismo email
            OrderVerification::where('email', $validated['email'])->delete();

            // Guardar el nuevo código
            OrderVerification::create([
                'email' => $validated['email'],
                'code' => $code,
                'expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($validated['email'])->send(
                new OrderVerificationMail($validated['name'], $code)
            );

            return response()->json(['success' => true, 'message' => 'Código enviado correctamente.']);

        } catch (\Throwable $e) {
            throw Handler::cannotSendVerificationCode();
        }

    }

    /**
     * Paso 2: Confirmar el pedido con el código recibido.
     */
    public function confirm(OrderRequest $request)
    {
        try {
            // ✅ Validación estricta de entrada
            $validated = $request->validate([
                'name' => 'required|string|max:150',
                'email' => 'required|email|max:150',
                'phone' => 'required|string|max:50',
                'address' => 'required|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'code' => 'required|string|max:6',
            ]);

            // ✅ Verificar el código temporal
            $verification = OrderVerification::where('email', $validated['email'])
                ->where('code', $validated['code'])
                ->first();

            if (!$verification || $verification->isExpired()) {
                throw Handler::invalidVerificationCode();
            }

            // Eliminar el registro de verificación (ya usado)
            $verification->delete();

            // ✅ Crear el pedido dentro de una transacción segura
            $order = DB::transaction(function () use ($validated) {
                $totalOrder = 0;

                // Crear la orden
                $order = Order::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'address' => $validated['address'],
                    'status' => 'confirmed',
                    'confirmation_status' => true,
                    'confirmed_at' => now(),
                ]);

                // Procesar cada ítem
                foreach ($validated['items'] as $itemData) {
                    $product = Product::lockForUpdate()->find($itemData['product_id']);
                    if (!$product) {throw Handler::productNotFound();}

                    if ($product->stock < $itemData['quantity']) {
                        throw Handler::insufficientStock($product->name);
                    }

                    $itemTotal = $product->price * $itemData['quantity'];
                    $totalOrder += $itemTotal;

                    // Crear ítem de pedido con total
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $itemData['quantity'],
                        'price' => $product->price,
                        'total' => $itemTotal, // ✅ importante
                    ]);

                    // Descontar del inventario
                    $product->decrement('stock', $itemData['quantity']);
                }

                // Actualizar el total en la orden
                $order->update(['total_amount' => $totalOrder]);

                return $order;
            });

            // ✅ Enviar correo de confirmación (sin romper si falla)
            try {

                Mail::to($order->email)->send(new OrderConfirmedMail($order));

            } catch (\Throwable $e) {
                \Log::warning("No se pudo enviar correo de confirmación final: " . $e->getMessage());
            }

            // ✅ Respuesta final al frontend
            return response()->json([
                'success' => true,
                'message' => 'Pedido confirmado con éxito.',
                'order' => $order->load('items.product'),
            ]);
        } catch (\Throwable $e) {
            throw Handler::cannotCompleteOrder();
        }
    }

    public function index()
    {
        $orders = Order::with('items.product')->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'orders' => $orders]);
    }

    public function stats()
    {
        $stats = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as orders_count'),
            DB::raw('SUM(total_amount) as total_sales')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
