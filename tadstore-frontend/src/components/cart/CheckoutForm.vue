<template>
  <div class="checkout-wrapper">

    <!-- 🧾 Paso 1: Formulario de datos -->
    <section v-if="step === 'form'" class="checkout-card">
      <h2 class="checkout-title">Datos de envío</h2>

      <form @submit.prevent="requestVerificationCode" class="checkout-form">
        <div class="form-group" v-for="field in fields" :key="field.id">
          <label :for="field.id">{{ field.label }}</label>
          <input
            :id="field.id"
            v-model="field.model.value"
            :type="field.type"
            :placeholder="field.placeholder"
            :disabled="isDisabled"
            :required="field.required"
          />
        </div>

        <button type="submit" class="btn-primary full-width" :disabled="isDisabled">
          <span>📩</span> Enviar código de verificación
        </button>
      </form>
    </section>

    <!-- 🟡 Paso 2: Ingreso del código -->
    <section v-else-if="step === 'verify'" class="checkout-card verify-section">
      <h2 class="checkout-title">Verifica tu compra</h2>

      <p class="verify-text">
        Hemos enviado un código de verificación a tu correo <strong>{{ email }}</strong>.
        Ingresa el código para confirmar tu pedido.
      </p>

      <input
        v-model="verificationCode"
        maxlength="6"
        placeholder="Ingresa tu código de 6 dígitos"
        class="verify-input"
      />

      <button class="btn-primary full-width" @click="confirmOrder">
        ✅ Confirmar compra
      </button>
    </section>

    <!-- 🟢 Paso 3: Confirmación final -->
    <section v-else-if="step === 'done'" class="checkout-card thankyou-message">
      <p>✅ El pedido ha sido realizado correctamente.</p>
      <p>¡Gracias por tu compra!</p>
    </section>

    <!-- 🧮 Resumen del pedido -->
    <section class="checkout-card summary-card">
      <h2 class="checkout-title">Resumen del pedido</h2>

      <div v-if="!items.length" class="empty-cart">Tu carrito está vacío.</div>

      <ul v-else class="product-list">
        <li v-for="(item, i) in items" :key="i" class="product-item">
          <span class="product-name">{{ item.name }}</span>
          <span class="product-quantity">x{{ item.quantity }}</span>
          <span class="product-price">{{ formatCurrency(item.price * item.quantity) }}</span>
        </li>
      </ul>

      <div v-if="items.length" class="total">
        <p><strong>Total:</strong> {{ formatCurrency(totalPrice) }}</p>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import api from '@/services/api'

import { useCart } from "../../composables/useCart";

const { items, totalPrice, clearCart } = useCart();

const step = ref("form"); // 'form' | 'verify' | 'done'
const isDisabled = ref(false);
const verificationCode = ref("");

const name = ref("");
const email = ref("");
const address = ref("");
const phone = ref("");

const fields = [
  { id: "name", label: "Nombre completo", type: "text", placeholder: "Ej: Juan Pérez", required: true, model: name },
  { id: "email", label: "Correo electrónico", type: "email", placeholder: "ejemplo@correo.com", required: true, model: email },
  { id: "direccion", label: "Dirección de envío", type: "text", placeholder: "Calle 123 #45-67, Bogotá", required: true, model: address },
  { id: "telefono", label: "Teléfono", type: "text", placeholder: "Ej: 3001234567", required: true, model: phone },
];

const formatCurrency = (value) =>
  new Intl.NumberFormat("es-CO", {
    style: "currency",
    currency: "COP",
    maximumFractionDigits: 0,
  }).format(value || 0);

// Paso 1: solicitar código de verificación
async function requestVerificationCode() {
  isDisabled.value = true;
  try {
    await api.post("/orders/send-code", {
      name: name.value,
      email: email.value,
    });
    step.value = "verify";
  } catch (error) {
    console.error(error);
    console.log("➡ Enviando petición a:", axios.defaults.baseURL + "/orders/send-code");
    alert("❌ No se pudo enviar el código de verificación.");
    isDisabled.value = false;
  }
}

// Paso 2: confirmar pedido con el código
async function confirmOrder() {
  try {
    const { data } = await api.post("/orders/confirm", {
      name: name.value,
      email: email.value,
      phone: phone.value,
      address: address.value,
      code: verificationCode.value,
      items: items.map((p) => ({
        product_id: p.id,
        quantity: p.quantity,
      })),
    });

    if (data.success) {
      step.value = "done";
      clearCart();

      // Cerrar modal luego de un breve mensaje
      setTimeout(() => {
        window.dispatchEvent(new CustomEvent("order-completed"));
      }, 2000);
    } else {
      alert("❌ Código incorrecto o expirado.");
    }
  } catch (error) {
    console.error(error);
    alert("❌ Error al confirmar la compra.");
  }
}
</script>

<style scoped>
/* ====== Layout general ====== */
.checkout-wrapper {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
  color: var(--color-text);
}

/* ====== Tarjetas ====== */
.checkout-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  padding: 1rem 1.2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
  transition: background 0.3s ease, transform 0.2s ease;
}
.checkout-card:hover {
  transform: translateY(-2px);
}

/* ====== Títulos ====== */
.checkout-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--color-accent);
  border-bottom: 1px solid var(--color-border);
  margin-bottom: 0.8rem;
  padding-bottom: 0.4rem;
}

/* ====== Formulario ====== */
.checkout-form {
  display: flex;
  flex-direction: column;
  gap: 0.9rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  color: var(--color-muted);
  font-size: 0.85rem;
  margin-bottom: 0.25rem;
}

.form-group input {
  background: var(--color-bg);
  color: var(--color-text);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  padding: 0.6rem 0.8rem;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-group input:focus {
  border-color: var(--color-accent);
  box-shadow: 0 0 0 2px rgba(255, 182, 0, 0.3);
  outline: none;
}

/* ====== Botones ====== */
.btn-primary {
  background: var(--color-accent);
  color: var(--color-black);
  font-weight: 700;
  border: none;
  border-radius: 8px;
  padding: 0.7rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  transition: opacity 0.25s ease;
}

.btn-primary:hover {
  opacity: 0.9;
}

.full-width {
  width: 100%;
}

/* ====== Verificación ====== */
.verify-section {
  text-align: center;
}

.verify-text {
  color: var(--color-muted);
  font-size: 0.9rem;
  margin-bottom: 1rem;
}

.verify-input {
  width: 100%;
  text-align: center;
  font-size: 1.2rem;
  letter-spacing: 3px;
  border: 1px solid var(--color-border);
  background: var(--color-bg);
  color: var(--color-text);
  border-radius: 8px;
  padding: 0.6rem;
  margin-bottom: 1rem;
}

/* ====== Confirmación final ====== */
.thankyou-message {
  text-align: center;
  color: var(--color-accent);
  font-weight: 600;
  font-size: 1.05rem;
  padding: 1.2rem 0;
  line-height: 1.4;
}

/* ====== Resumen ====== */
.summary-card {
  margin-top: 0.5rem;
}

.product-list {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 180px;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: transparent transparent;
}
.product-list::-webkit-scrollbar {
  width: 6px;
}
.product-list::-webkit-scrollbar-thumb {
  background-color: transparent;
}
.product-list:hover::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.1);
}

.product-item {
  display: flex;
  justify-content: space-between;
  border-bottom: 1px solid var(--color-border);
  padding: 0.4rem 0;
  font-size: 0.9rem;
}

.product-name {
  color: var(--color-text);
}

.product-quantity {
  color: var(--color-muted);
}

.product-price {
  color: var(--color-accent);
  font-weight: 600;
}

/* ====== Total ====== */
.total {
  text-align: right;
  margin-top: 1rem;
  font-weight: bold;
  color: var(--color-accent);
}

/* ====== Carrito vacío ====== */
.empty-cart {
  text-align: center;
  color: var(--color-muted);
  padding: 1.5rem 0;
  font-style: italic;
}

/* ====== Responsive ====== */
@media (min-width: 768px) {
  .checkout-wrapper {
    flex-direction: row;
  }
  .checkout-card {
    flex: 1;
  }
}
</style>
