<template>
  <div class="modal-backdrop" @click.self="$emit('close')">
    <div class="modal">
      <header class="modal-header"> 
          <template v-if="!checkoutMode">  
            <h3>Tu carrito 🛍️</h3>
          </template>
          <template v-else>
            <h3>Finalizar compra 🛒</h3>
          </template>
          <button class="close-btn" @click="$emit('close')">✖</button>
        </header>


      <template v-if="!checkoutMode">  
        <section v-if="items.length" class="modal-body">
          <CartItem
            v-for="item in items"
            :key="item.id"
            :item="item"
            @removed="onRemoved"
          />
        </section>

        <section v-else class="empty-cart">Tu carrito está vacío.</section>

        <footer v-if="items.length" class="modal-footer">
          <div>
            <p class="total">Items: {{ totalItems }} • Total: {{ formatCurrency(totalPrice) }}</p>
            <p class="hint">Los precios son simulados (sandbox).</p>
          </div>
          
          <div class="actions">
            <button class="clear-btn" @click="confirmClear">Vaciar</button>


            <button class="checkout-btn" @click="checkoutMode = true">
              <span class="icon">💳</span> Ir al pago
            </button>
          </div>
        </footer>
      </template>
      <template v-else>
        <CheckoutForm />
      </template>
      </div>
  </div>
</template>

<script setup>
import { ref, onBeforeUnmount} from "vue";
import { useRouter } from 'vue-router'
import { useCart } from '@/composables/useCart'
import CartItem from './CartItem.vue'
import CheckoutForm from './CheckoutForm.vue'

const checkoutMode = ref(false) 

onBeforeUnmount(() => {
  window.removeEventListener("order-completed", () => {
    checkoutMode.value = false;
  });
});

const { items, totalItems, totalPrice, clearCart } = useCart()

const onRemoved = (id) => {
  // opcional: manejar algo cuando un item es removido
}

const confirmClear = () => {
  if (confirm('¿Estás seguro de vaciar el carrito?')) {
    clearCart()
  }
}

const formatCurrency = (value) =>
  new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0,
  }).format(value || 0)
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal {
  background: var(--color-surface);
  color: var(--color-text);
  border-radius: var(--radius);
  padding: 1.5rem;
  width: 90%;
  max-width: 450px;
  box-shadow: var(--shadow-elevated);
  max-height: 90vh;
  overflow-y: auto;
  transition: all 0.3s ease;

  scrollbar-width: thin;
  scrollbar-color: var(--color-bg) transparent;
}

.modal::-webkit-scrollbar {
  width: 6px;
}
.modal::-webkit-scrollbar-thumb {
  background-color: transparent;
}
.modal:hover::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.1); /* visible solo al pasar el mouse */
}

@media (min-width:900px ) {
  .modal:has(.checkout-wrapper) {
    max-width: 720px;
    display: flex;
    flex-direction: column;
    overflow-y: visible;
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid var(--color-border);
  padding-bottom: 0.5rem;
}

.close-btn {
  background: transparent;
  border: 1px solid var(--color-accent);
  border-radius: 6px;
  color: var(--color-accent);
  font-size: 1.2rem;
  cursor: pointer;
}
.close-btn:hover {
  background: var(--color-accent);
  color: var(--color-bg);

}

.modal-body {
  margin-top: 1rem;
  max-height: 300px;
  overflow-y: auto;
}

.modal-footer {
  margin-top: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.total {
  font-weight: bold;
  color: var(--color-accent);
}

.clear-btn {
  border: 1px solid var(--color-accent);
  color: var(--color-accent);
  background: transparent;
  border-radius: 6px;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: 0.3s;
}

.clear-btn:hover {
  background: var(--color-accent);
  color: var(--color-bg);
}

.empty-cart {
  text-align: center;
  color: var(--color-muted);
  padding: 2rem 0;
}
.checkout-btn{
  background:var(--color-accent);
  color:var(--color-bg);
  border:none;
  padding:0.6rem 0.9rem;
  border-radius:8px;
  cursor:pointer;
  font-weight:700;
  display:flex;
  align-items:center;
  gap:8px
}

.clear-btn{
  background:transparent;
  border:1px solid var(--color-accent);
  color:var(--color-accent);
  padding:0.5rem;border-radius:8px
}
.actions{
  display:flex;
  gap:0.6rem;
  align-items:center
}

.hint{
  font-size:0.75rem;
  color:var(--color-muted);
  margin:0
}



</style>

