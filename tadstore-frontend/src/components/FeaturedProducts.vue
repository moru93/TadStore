<template>
  <section class="featured">
    <h3 class="section-title">Productos destacados</h3>

    <div v-if="loading" class="loading">Cargando...</div>
    <div v-else class="featured-grid">
      <div
        v-for="(product, i) in products.slice(0, 6)"
        :key="product.id || i"
        class="featured-card"
      >
        <img
          :src="product.image_url || defaultImage"
          class="featured-image"
          :alt="product.name"
          loading="lazy"
        />
        <h4>{{ product.name }}</h4>
        <p class="price">{{ formatCurrency(product.price) }}</p>
        <button class="view-btn" @click="$router.push('/products')">
          Ver más
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
const products = ref([]);
const loading = ref(true);
const defaultImage =
  "https://placehold.co/300x200.png?text=Producto";

onMounted(async () => {
  try {
    const { data } = await axios.get("http://127.0.0.1:8000/api/products");
    products.value = Array.isArray(data.data)
      ? data.data
      : data.products || data || [];
  } finally {
    loading.value = false;
  }
});

const formatCurrency = (value) =>
  new Intl.NumberFormat("es-CO", {
    style: "currency",
    currency: "COP",
    maximumFractionDigits: 0,
  }).format(value || 0);
</script>

<style scoped>
.featured {
  padding: 5rem 1rem;
  text-align: center;
  background: var(--color-bg-alt);
}
.section-title {
  font-size: 2rem;
  margin-bottom: 2rem;
  color: var(--color-accent);
}
.featured-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 2rem;
}
.featured-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  padding: 1rem;
  transition: transform 0.3s ease, border-color 0.3s ease;
}
.featured-card:hover {
  transform: translateY(-6px);
  border-color: var(--color-accent);
}
.featured-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 1rem;
}
.price {
  color: var(--color-accent);
  font-weight: 700;
  margin-bottom: 0.8rem;
}
.view-btn {
  background: transparent;
  border: 1px solid var(--color-accent);
  color: var(--color-accent);
  padding: 0.6rem 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}
.view-btn:hover {
  background: var(--color-accent);
  color: var(--color-bg);
}
</style>
