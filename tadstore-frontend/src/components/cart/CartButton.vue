<template>
  <div class="cart-wrapper">
    <button class="cart-button" @click="toggleModal">
      🛒
      <span v-if="totalItems > 0" class="badge">{{ totalItems }}</span>
    </button>

    <CartModal v-if="showModal" @close="toggleModal" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useCart } from '@/composables/useCart'
import CartModal from './CartModal.vue'

const { totalItems } = useCart()
const showModal = ref(false)

const toggleModal = () => (showModal.value = !showModal.value)
</script>

<style scoped>
.cart-wrapper {
  position: relative;
}

.cart-button {
  background: transparent;
  border: none;
  color: var(--color-accent);
  font-size: 1.4rem;
  cursor: pointer;
  position: relative;
}

.badge {
  position: absolute;
  top: -5px;
  right: -8px;
  background: var(--color-accent);
  color: var(--color-bg);
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 0.7rem;
  font-weight: bold;
}
</style>
