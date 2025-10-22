<template>
  <header class="header">
    <div class="header-content">
      <img src="@/assets/TAD - LOGO.png" alt="TadStore Logo" class="logo-img" />

      <nav class="nav-menu">
        <!-- Mostrar el carrito solo para usuarios normales -->
        <CartButton v-if="!isAdmin" class="nav-link cart" />

        <!-- Links dinámicos -->
        <RouterLink
          v-for="link in filteredLinks"
          :key="link.to"
          :to="link.to"
          :class="['nav-link', { 'login-btn': link.text === 'Login' }]"
        >
          {{ link.text }}
        </RouterLink>

        <!-- Mostrar nombre y logout si está logueado -->
        <div v-if="user" class="user-section">
          <span class="user-name">👤 {{ user.name }}</span>
          <button class="logout-btn" @click="logout">Cerrar sesión</button>
        </div>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import CartButton from './cart/CartButton.vue'
import { useUserStore } from '@/stores/user'

const router = useRouter()
const userStore = useUserStore()
const user = computed(() => userStore.user)
const isAdmin = computed(() => user.value?.role === 'admin')

// 🔗 Definición de enlaces por tipo de usuario
const linksPublic = [
  { text: 'Nosotros', to: '/' },
  { text: 'Tienda', to: '/products' },
  { text: 'Login', to: '/login' },
]

const linksAdmin = [
  { text: 'Panel Admin', to: '/admin/dashboard' },
  { text: 'Productos', to: '/admin/products' },
  { text: 'Categorías', to: '/admin/categories' },
  { text: 'Ventas', to: '/admin/orders' },
  // { text: 'Contactos', to: '/admin/contacts' },
]

// 👇 Selección dinámica según rol
const filteredLinks = computed(() => {
  if (!user.value) return linksPublic
  if (isAdmin.value) return linksAdmin
  return linksPublic.filter(l => l.text !== 'Login')
})

// 🚪 Cerrar sesión
const logout = () => {
  userStore.logout()
  router.push('/')
}
</script>

<style scoped>
.header {
  background: linear-gradient(90deg, #222 0%, #2a2a2a 100%);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
  padding: 1rem 2rem;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1100px;
  margin: 0 auto;
}

.nav-menu {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.cart {
  bottom: 5px;
}

.nav-link {
  color: #ffffff;
  text-decoration: none;
  font-weight: 500;
  position: relative;
  transition: color 0.3s ease;
}

.nav-link::after {
  content: "";
  position: absolute;
  bottom: -3px;
  left: 0;
  height: 2px;
  width: 0;
  background-color: var(--color-accent);
  transition: width 0.3s ease;
}

.nav-link:hover {
  color: var(--color-accent);
}

.nav-link:hover::after {
  width: 100%;
}

.login-btn {
  background: #000;
  color: var(--color-accent);
  padding: 0.4rem 1rem;
  border-radius: 6px;
  font-weight: 600;
  border: 1px solid var(--color-accent);
  transition: 0.3s;
  bottom: 5px;
}

.login-btn:hover {
  background: var(--color-accent);
  color: #000;
  border: 2px solid #000;
}

.logo-img {
  height: 40px;
  width: auto;
  margin-right: 0.75rem;
  object-fit: contain;
}

.user-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-name {
  color: #fff;
  font-size: 0.95rem;
  font-weight: 500;
}

.logout-btn {
  background: #ef4444;
  color: #fff;
  border: none;
  padding: 0.4rem 0.75rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.logout-btn:hover {
  background: #dc2626;
}
</style>
