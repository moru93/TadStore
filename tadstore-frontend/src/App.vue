<template>
  <div id="app" class="app-container">
    <!-- Header -->
    <Navbar />
    <!-- Main content -->
    <main class="main-content">
      <RouterView />
    </main>
    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import Footer from './components/Footer.vue';
import Navbar from './components/Navbar.vue';

</script>

<style>

/* ===============================
   LAYOUT GENERAL
   =============================== */
.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: url('@/assets/Background.png');
  color: var(--color-text);
  font-family: "Inter", "Segoe UI", sans-serif;
}

/* ===============================
   HEADER
   =============================== */
.header {
  background: #1e1e1e;
  box-shadow: 0 2px 6px rgba(0,0,0,0.5);
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

.logo {
  font-size: 1.6rem;
  font-weight: 700;
  color: var(--color-accent);
  letter-spacing: 0.5px;
}

.logo span {
  color: var(--color-text);
}

/* ===============================
   MAIN
   =============================== */
.main-content {
  flex: 1;
  padding: 2rem;
  max-width: 1100px;
  margin: 0 auto;
}


</style>
