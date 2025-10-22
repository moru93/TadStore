<template>
  <div class="checkout-page">
    <h2>Finalizar compra</h2>

    <form @submit.prevent="simulatePayment" class="checkout-form">
      <input v-model="form.name" placeholder="Nombre completo" required />
      <input v-model="form.email" type="email" placeholder="Correo" required />
      <input v-model="form.address" placeholder="Dirección" required />
      <input v-model="form.phone" placeholder="Teléfono" required />
      <div class="summary">
        <p>Items: {{ totalItems }} • Total: {{ formatCurrency(totalPrice) }}</p>
      </div>
      <button class="pay-btn" :disabled="processing">{{ processing ? 'Procesando...' : 'Simulate Payment' }}</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCart } from '@/composables/useCart'

const { totalItems, totalPrice, createOrder } = useCart()
const router = useRouter()
const form = ref({ name:'', email:'', address:'', phone:'' })
const processing = ref(false)

const simulatePayment = async () => {
  processing.value = true
  try {
    const resp = await createOrder(form.value) // backend creates order and sends code
    // resp should contain { order: { id, ...}, confirmation_code_present?: boolean }
    router.push({ name: 'VerifyCode', params: { orderId: resp.order.id } })
  } catch (err) {
    alert('Error al procesar el pedido.')
    console.error(err)
  } finally {
    processing.value = false
  }
}

const formatCurrency = (v)=> new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0}).format(v||0)
</script>

<style scoped>
.checkout-form{display:flex;flex-direction:column;gap:0.75rem;max-width:540px}
.pay-btn{background:var(--color-accent);color:var(--color-bg);padding:0.8rem;border-radius:8px;border:none;cursor:pointer}
</style>
