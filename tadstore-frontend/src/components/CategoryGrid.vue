<template>
  <section class="categories">
    <h3 class="section-title">Explora por categoría</h3>
    <div class="category-grid">
      <div
        v-for="(cat, i) in categories"
        :key="i"
        class="category-card"
        @click="$router.push('/products')"
      >
        <img :src="cat.image" :alt="cat.name" />
        <h4>{{ cat.name }}</h4>
      </div>
    </div>
  </section>
</template>

<script setup>
const categories = [
  { name: "Tecnología", image: "https://placehold.co/150x150?text=Tech" },
  { name: "Hogar", image: "https://placehold.co/150x150?text=Home" },
  { name: "Moda", image: "https://placehold.co/150x150?text=Moda" },
  { name: "Deporte", image: "https://placehold.co/150x150?text=Sport" },
];
</script>

<style scoped>
.categories {
  padding: 3rem 1rem;
  text-align: center;
}
.section-title {
  font-size: 1.8rem;
  margin-bottom: 2rem;
  color: var(--color-accent);
}
.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 2rem;
}
.category-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 10px;
  padding: 1rem;
  cursor: pointer;
  transition: transform 0.3s ease, border-color 0.3s ease;
}
.category-card:hover {
  transform: translateY(-6px);
  border-color: var(--color-accent);
}
.category-card img {
  width: 100%;
  height: 100px;
  object-fit: cover;
  margin-bottom: 1rem;
  border-radius: 8px;
}
.category-card h4 {
  color: var(--color-text);
  font-weight: 600;
}
</style>
