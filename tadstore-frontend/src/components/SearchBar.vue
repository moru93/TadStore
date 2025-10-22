<template>
  <div class="search-bar">
    <input
      type="text"
      v-model="query"
      @input="onSearch"
      placeholder="Buscar producto..."
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
const emit = defineEmits(['search'])

const query = ref('')
const onSearch = () => emit('search', query.value.trim())
</script>

<style scoped>
.search-bar {
  flex: 1;
  min-width: 220px;
}

input {
  width: 100%;
  padding: 0.6rem 1rem;
  border-radius: 6px;
  border: 1px solid var(--color-border);
  background: var(--color-surface);
  color: var(--color-text);
  outline: none;
  transition: border-color 0.3s ease;
}

input:focus {
  border-color: var(--color-accent);
}
</style>
