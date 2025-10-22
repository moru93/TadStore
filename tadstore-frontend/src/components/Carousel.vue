<template>
  <div class="carousel">
    <div
      v-for="(slide, index) in slides"
      :key="index"
      class="slide"
      :class="{ active: index === currentSlide }"
    >
      <img :src="slide.image" :alt="slide.title" class="slide-image" />
      <div class="overlay">
        <h2>{{ slide.title }}</h2>
        <p>{{ slide.subtitle }}</p>
        <button class="cta-btn" @click="$router.push('/products')">
          Ver productos
        </button>
      </div>
    </div>

    <div class="controls">
      <span
        v-for="(slide, index) in slides"
        :key="'dot-' + index"
        class="dot"
        :class="{ active: index === currentSlide }"
        @click="goTo(index)"
      ></span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const slides = [
  {
    image: "https://placehold.co/1600x600?text=Promoción+1",
    title: "Bienvenido a TadStore 🛍️",
    subtitle: "Descubre lo último en tecnología y estilo.",
  },
  {
    image: "https://placehold.co/1600x600?text=Ofertas+especiales",
    title: "Ofertas especiales",
    subtitle: "Aprovecha descuentos exclusivos por tiempo limitado.",
  },
  {
    image: "https://placehold.co/1600x600?text=Calidad+Garantizada",
    title: "Calidad garantizada",
    subtitle: "Productos seleccionados con los mejores estándares.",
  },
];

const currentSlide = ref(0);
let intervalId;

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.length;
};
const goTo = (i) => (currentSlide.value = i);

onMounted(() => {
  intervalId = setInterval(nextSlide, 5000);
});
onUnmounted(() => clearInterval(intervalId));
</script>

<style scoped>
.carousel {
  position: relative;
  overflow: hidden;
  width: 100%;
  height: 70vh;
  max-height: 600px;
}
.slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity 1s ease;
}
.slide.active {
  opacity: 1;
  z-index: 1;
}
.slide-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(0.7);
}
.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  color: white;
  background: rgba(0, 0, 0, 0.25);
}
.overlay h2 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}
.overlay p {
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
}
.cta-btn {
  background-color: var(--color-accent);
  border: none;
  color: var(--color-bg);
  padding: 0.8rem 1.5rem;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
}
.cta-btn:hover {
  background: transparent;
  border: 1px solid var(--color-accent);
  color: var(--color-accent);
}
.controls {
  position: absolute;
  bottom: 15px;
  left: 50%;
  transform: translateX(-50%);
}
.dot {
  height: 10px;
  width: 10px;
  margin: 0 5px;
  background-color: #aaa;
  border-radius: 50%;
  display: inline-block;
  cursor: pointer;
}
.dot.active {
  background-color: var(--color-accent);
}
</style>
