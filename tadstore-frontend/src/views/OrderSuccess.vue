<template>
  <div class="success">
    <h2>Compra realizada ✅</h2>
    <p>Tu pedido #{{ orderId }} fue confirmado. Recibirás un correo con el detalle.</p>
    <RouterLink to="/">Volver al inicio</RouterLink>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
const route = useRoute()
const orderId = route.params.orderId
const order = ref(null)

onMounted(async () => {
  try { const { data } = await axios.get(`http://127.0.0.1:8000/api/orders/${orderId}`); order.value = data.order } catch {}
})
</script>
