<template>
  <div class="home">
    <Carousel />
    <CategoryGrid />
    <FeaturedProducts />

    <section class="about">
      <h3>¿Por qué elegir TadStore?</h3>
      <p>
        Somos una tienda digital enfocada en calidad, servicio y satisfacción.
        Trabajamos con las mejores marcas para ofrecerte confianza y respaldo
        en cada compra.
      </p>
    </section>
  </div>
</template>

<script setup>
import Carousel from "../components/Carousel.vue";
import CategoryGrid from "../components/CategoryGrid.vue";
import FeaturedProducts from "../components/FeaturedProducts.vue";
</script>

<style scoped>
.home {
  width: 100%;
  overflow-x: hidden;
  background: var(--color-bg);
  color: var(--color-text);
}
.about {
  text-align: center;
  padding: 3rem 1rem;
  max-width: 900px;
  margin: 0 auto;
}
.about h3 {
  color: var(--color-accent);
  font-size: 1.8rem;
  margin-bottom: 1rem;
}
.about p {
  font-size: 1.1rem;
  line-height: 1.6;
  color: var(--color-muted);
}

</style>
