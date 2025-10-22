<template>
  <footer class="footer">
    <p>
      © {{ new Date().getFullYear() }} — Creado con 💛 para la evaluación técnica Iyata
    </p>
  </footer>
</template>

<script setup></script>

<style scoped>
.footer {
  text-align: center;
  padding: 1rem;
  border-top: 1px solid var(--color-border);
  background: #1e1e1e;
  color: #bbb;
  font-size: 0.9rem;
  margin-top: 3rem;
}
</style>
