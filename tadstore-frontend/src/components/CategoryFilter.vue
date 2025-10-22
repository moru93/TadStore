<template>
  <div class="category-filter">
    <label for="category-select">Categoría:</label>
    <select
      id="category-select"
      v-model="selectedCategory"
      @change="$emit('change', selectedCategory)"
    >
      <option value="">Todas</option>
      <option v-for="cat in categories" :key="cat.id" :value="cat.id">
        {{ cat.name }}
      </option>
    </select>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  apiUrl: { type: String, default: 'http://127.0.0.1:8000/api/categories' },
})
const emit = defineEmits(['change'])

const categories = ref([])
const selectedCategory = ref('')

onMounted(async () => {
  try {
    const { data } = await axios.get(props.apiUrl)
    categories.value = Array.isArray(data.data) ? data.data : data
  } catch (err) {
    console.error('Error cargando categorías:', err)
  }
})
</script>

<style scoped>
.category-filter {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

label {
  font-weight: 600;
  color: var(--color-accent);
}

select {
  background: var(--color-surface);
  color: var(--color-text);
  border: 1px solid var(--color-border);
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  outline: none;
  font-size: 0.95rem;
  transition: border-color 0.3s ease;
}

select:hover,
select:focus {
  border-color: var(--color-accent);
}
</style>
