@component('mail::message')
# ¡Gracias por tu compra, {{ $order->name }}! 🎉

Tu pedido **#{{ $order->id }}** ha sido confirmado con éxito.

---

### 🧾 Resumen de tu pedido
@component('mail::table')
| Producto | Cantidad | Precio Unitario | Total |
|:----------|:----------:|:---------------:|------:|
@foreach ($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ${{ number_format($item->price, 0, ',', '.') }} | ${{ number_format($item->total, 0, ',', '.') }} |
@endforeach
@endcomponent

**Total a pagar:** ${{ number_format($order->total_amount, 0, ',', '.') }}

📦 Dirección de envío:  
{{ $order->address }}

📞 Teléfono de contacto:  
{{ $order->phone }}

---

Si tienes alguna pregunta, contáctanos respondiendo este correo.  
Gracias por confiar en **{{ config('app.name') }}** 💛

@component('mail::button', ['url' => config('app.url')])
Ir al sitio web
@endcomponent

@endcomponent
