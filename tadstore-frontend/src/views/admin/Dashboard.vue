<template>
  <div class="dashboard">
    <h1>Panel de Administración</h1>

    <div class="stats">
      <div class="stat-card">
        <h2>🛍️ Productos</h2>
        <p>{{ stats.products }}</p>
      </div>

      <div class="stat-card">
        <h2>📂 Categorías</h2>
        <p>{{ stats.categories }}</p>
      </div>

      <div class="stat-card">
        <h2>💰 Pedidos</h2>
        <p>{{ stats.orders }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>

import api from '@/services/api';
import { ref, onMounted } from 'vue';
import { useUserStore } from '@/stores/user';
import { useRouter } from 'vue-router';

const router = useRouter();
const userStore = useUserStore();

const stats = ref({
  products: 0,
  categories: 0,
  orders: 0,
});

onMounted(async () => {
  // Solo admin puede acceder
  if (!userStore.user || userStore.user.role !== 'admin') {
    router.push('/login');
    return;
  }

  try {
    const [p, c, o] = await Promise.all([
      api.get('/products'),
      api.get('/categories'),
      api.get('/orders'),
    ]);

    stats.value = {
      products: p.data?.total || 0, // p.data es el response Axios, p.data.data es el arreglo real
      categories: c.data?.total || 0,
      orders: Array.isArray(o.data?.orders) ? o.data.orders.length : 0,
    };

  } catch (error) {
    console.error('Error al obtener estadísticas:', error);
    alert('Error al cargar las estadísticas. Intente nuevamente.');
  }
});
</script>

<style scoped>
.dashboard {
  padding: 2rem;
  min-height: 100vh;
  background-color: #111;
  color: #fff;
}

h1 {
  text-align: center;
  margin-bottom: 2rem;
  font-size: 1.8rem;
}

.stats {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 1.5rem;
}

.stat-card {
  background-color: #1e1e1e;
  padding: 1.5rem 2rem;
  border-radius: 10px;
  width: 200px;
  text-align: center;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.4);
  transition: transform 0.3s;
}

.stat-card:hover {
  transform: translateY(-4px);
}

.stat-card h2 {
  font-size: 1.2rem;
  color: var(--color-accent, #00c4b4);
  margin-bottom: 0.5rem;
}

.stat-card p {
  font-size: 2rem;
  font-weight: 600;
}
</style>
