// src/composables/useCart.js
import { reactive, computed, watch } from 'vue'
import axios from 'axios'

const STORAGE_KEY = 'tadstore_cart'
const API_BASE = 'http://127.0.0.1:8000/api'

const state = reactive({ items: loadFromStorage() })

function loadFromStorage() {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    return stored ? JSON.parse(stored) : []
  } catch {
    return []
  }
}

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


export function useCart() {
  const saveToStorage = () => localStorage.setItem(STORAGE_KEY, JSON.stringify(state.items))

  const addToCart = (product) => {
    const existing = state.items.find((p) => p.id === product.id);

    if (existing) {
      if (existing.quantity < product.stock) {
        existing.quantity += 1;
        showToast(`🛒 Se agregó otra unidad de ${product.name}`);
      } else {
        showToast(`⚠️ Ya has agregado todas las unidades disponibles (${product.stock})`, "warning");
      }
    } else {
      if (product.stock > 0) {
        state.items.push({ ...product, quantity: 1 });
        showToast(`✅ ${product.name} agregado al carrito`);
      } else {
        showToast(`❌ ${product.name} está agotado`, "error");
      }
    }
  };

  const removeFromCart = (id) => {
    const idx = state.items.findIndex(p => p.id === id)
    if (idx !== -1) state.items.splice(idx, 1)
  }

  const updateQuantity = (id, qty) => {
    const item = state.items.find(p => p.id === id)
    if (item) item.quantity = Math.max(1, qty)
  }

  const clearCart = () => state.items.splice(0, state.items.length)

  const totalItems = computed(() => state.items.reduce((s, p) => s + p.quantity, 0))
  const totalPrice = computed(() => state.items.reduce((s, p) => s + (p.price || 0) * p.quantity, 0))

  // createOrder: POST /api/orders (simula pago y devuelve order + confirmation_code)
  const createOrder = async (customer) => {
    // customer: { name, email, address, phone }
    const payload = {
      customer,
      items: state.items.map(i => ({ product_id: i.id, quantity: i.quantity, price: i.price })),
      total: totalPrice.value
    }
    const res = await axios.post(`${API_BASE}/orders`, payload)
    return res.data // { order: {...}, confirmation_code: '123456' } (backend)
  }

  // verify code endpoint
  const verifyOrderCode = async (orderId, code) => {
    const res = await axios.post(`${API_BASE}/orders/${orderId}/verify`, { code })
    return res.data // { success: true, order: {...} }
  }

  watch(() => state.items, saveToStorage, { deep: true })

  return {
    items: state.items,
    addToCart,
    removeFromCart,
    updateQuantity,
    clearCart,
    totalItems,
    totalPrice,
    createOrder,
    verifyOrderCode,
  }
}
