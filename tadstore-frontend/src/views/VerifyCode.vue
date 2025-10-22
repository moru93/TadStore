<template>
  <div class="verify-page">
    <h2>Confirm your order</h2>
    <p>We sent a confirmation code to your email. Enter it below:</p>
    <form @submit.prevent="verify">
      <input v-model="code" placeholder="Código (6 dígitos)" required />
      <button :disabled="verifying">{{ verifying ? 'Verificando...' : 'Verificar' }}</button>
    </form>
    <p v-if="message" class="msg">{{ message }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCart } from '@/composables/useCart'

const route = useRoute()
const router = useRouter()
const orderId = route.params.orderId
const code = ref('')
const verifying = ref(false)
const message = ref('')

const { verifyOrderCode, clearCart } = useCart()

const verify = async () => {
  verifying.value = true
  try {
    const res = await verifyOrderCode(orderId, code.value)
    if (res.success) {
      message.value = 'Compra confirmada. ¡Gracias!'
      // Limpiar carrito local (ya fue registrada)
      clearCart()
      // redirigir a page de "order success"
      setTimeout(() => router.push({ name: 'OrderSuccess', params: { orderId } }), 900)
    } else {
      message.value = 'Código incorrecto.'
    }
  } catch (err) {
    console.error('Error verifying order code:', err)
    message.value = 'Error al verificar.'
  } finally {
    verifying.value = false
  }
}
</script>

<style scoped>
input{padding:0.6rem;border-radius:6px;border:1px solid var(--color-border);background:var(--color-surface);color:var(--color-text)}
button{margin-top:0.6rem;padding:0.6rem 0.9rem;border-radius:6px;border:none;background:var(--color-accent);color:var(--color-bg)}
.msg{margin-top:0.6rem;color:var(--color-muted)}
</style>
