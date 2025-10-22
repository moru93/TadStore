<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      background: #1e1e1e;
      color: #eaeaea;
      font-family: 'Helvetica Neue', Arial, sans-serif;
      padding: 0;
      margin: 0;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      background: #2a2a2a;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid #3a3a3a;
    }
    .header {
      text-align: center;
      background: #000;
      padding: 25px 0;
    }
    .header img {
      width: 140px;
    }
    .content {
      padding: 24px;
    }
    h2 {
      color: #ffb600;
      margin-bottom: 12px;
      font-size: 20px;
      text-align: center;
    }
    .code {
      text-align: center;
      background: #000;
      color: #ffb600;
      font-size: 28px;
      font-weight: bold;
      letter-spacing: 4px;
      padding: 16px 0;
      margin: 20px 0;
      border-radius: 8px;
    }
    .summary {
      margin-top: 25px;
      border-top: 1px solid #3a3a3a;
      padding-top: 20px;
    }
    .product {
      display: flex;
      align-items: center;
      margin-bottom: 12px;
    }
    .product img {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      object-fit: cover;
      margin-right: 12px;
    }
    .product-info {
      flex: 1;
    }
    .product-name {
      color: #eaeaea;
      font-size: 14px;
      margin: 0;
    }
    .product-qty {
      color: #bdbdbd;
      font-size: 13px;
    }
    .product-price {
      color: #ffb600;
      font-weight: bold;
      font-size: 14px;
    }
    .total {
      text-align: right;
      margin-top: 16px;
      color: #ffb600;
      font-weight: bold;
      font-size: 16px;
    }
    .footer {
      text-align: center;
      padding: 20px;
      background: #1e1e1e;
      color: #888;
      font-size: 12px;
    }
    @media (max-width: 600px) {
      .content { padding: 16px; }
      .product img { width: 50px; height: 50px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="{{ asset('images/TAD - LOGO.png') }}" alt="TadStore Logo" />

    </div>
    <div class="content">
      <h2>¡Hola {{ $name }}!</h2>
      <p>Gracias por tu pedido en <strong>Mondesa</strong>. Antes de procesarlo, necesitamos verificar tu correo electrónico.</p>
      <p>Usa el siguiente código de verificación para continuar con tu compra:</p>
      <div class="code">{{ $code }}</div>

      @if(!empty($items))
      <div class="summary">
        <h3 style="color:#ffb600;">Resumen del pedido</h3>
        @foreach($items as $item)
        <div class="product">
          <!-- <img src="{{ $item['image'] ?? asset('images/product_placeholder.jpg') }}" alt="{{ $item['name'] }}"> -->
          <div class="product-info">
            <p class="product-name">{{ $item['name'] }}</p>
            <p class="product-qty">Cantidad: {{ $item['quantity'] }}</p>
          </div>
          <span class="product-price">${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
        </div>
        @endforeach

        <div class="total">Total: ${{ number_format($total, 0, ',', '.') }}</div>
      </div>
      @endif
    </div>

    <div class="footer">
      © {{ date('Y') }} Mondesa — Todos los derechos reservados.
    </div>
  </div>
</body>
</html>
