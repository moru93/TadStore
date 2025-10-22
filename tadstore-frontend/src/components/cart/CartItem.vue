<template>
  <transition name="fade-scale">
    <div class="cart-item" v-show="visible">
      <img :src="item.image_url || placeholder" class="thumb" alt="item.name"/>
      <div class="info">
        <p class="name">{{ item.name }}</p>
        <p class="price">{{ formatCurrency(item.price) }}</p>
        <div class="qty-controls">
          <button @click="decrease">−</button>
          <span>{{ item.quantity }}</span>
          <button @click="increase">＋</button>
        </div>
      </div>
      <button class="remove" @click="handleRemove">🗑</button>
    </div>
  </transition>
</template>

<script setup>
import { ref } from 'vue'
import { useCart } from '@/composables/useCart'

// Props y eventos
const props = defineProps({ item: Object })
const emit = defineEmits(['removed'])

// Recursos y estado
const placeholder = 'https://placehold.co/50x50?text=Sin+imagen'
const visible = ref(true)
const { updateQuantity, removeFromCart } = useCart()

// --- Helper para mostrar notificaciones visuales ---
function showToast(message, type = "info") {
  const colors = {
    info: "#007bff",
    success: "#28a745",
    warning: "#ffc107",
    error: "#dc3545"
  };

  const toast = document.createElement("div");
  toast.textContent = message;
  toast.style.position = "fixed";
  toast.style.bottom = "20px";
  toast.style.right = "20px";
  toast.style.padding = "12px 18px";
  toast.style.borderRadius = "8px";
  toast.style.backgroundColor = colors[type] || "#444";
  toast.style.color = "white";
  toast.style.fontSize = "0.9rem";
  toast.style.zIndex = 9999;
  toast.style.boxShadow = "0 4px 12px rgba(0,0,0,0.3)";
  toast.style.opacity = "0";
  toast.style.transition = "opacity 0.3s ease";

  document.body.appendChild(toast);
  setTimeout(() => (toast.style.opacity = "1"), 10);
  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => document.body.removeChild(toast), 300);
  }, 2500);
}

// --- Formateo de moneda ---
const formatCurrency = (v) =>
  new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    maximumFractionDigits: 0
  }).format(v || 0)

// --- Quitar producto ---
const handleRemove = () => {
  visible.value = false
  setTimeout(() => {
    removeFromCart(props.item.id)
    emit('removed', props.item.id)
  }, 220)
}

// --- Incrementar y decrementar con control de stock ---
const increase = () => {
  const { item } = props
  if (item.quantity < item.stock) {
    updateQuantity(item.id, item.quantity + 1)
    showToast(`🛒 Se agregó otra unidad de ${item.name}`)
  } else {
    showToast(`⚠️ Solo hay ${item.stock} unidades disponibles`, "warning")
  }
}

const decrease = () => {
  const { item } = props
  if (item.quantity > 1) {
    updateQuantity(item.id, item.quantity - 1)
  } else {
    handleRemove()
  }
}
</script>

<style scoped>
.cart-item{display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0;border-bottom:1px solid var(--color-border)}
.thumb{width:50px;height:50px;object-fit:cover;border-radius:6px}
.info{flex:1}
.qty-controls{display:flex;gap:0.5rem;align-items:center}
.qty-controls button{background:transparent;border:1px solid var(--color-border);padding:0 8px;border-radius:4px;color:var(--color-text);cursor:pointer}
.remove{background:transparent;border:none;cursor:pointer;color:var(--color-muted);font-size:1.1rem}

/* transition */
.fade-scale-enter-active,.fade-scale-leave-active{transition:all .22s ease}
.fade-scale-enter-from{opacity:0;transform:scale(.95)}
.fade-scale-leave-to{opacity:0;transform:scale(.9);height:0;margin:0;padding:0;overflow:hidden}
</style>
