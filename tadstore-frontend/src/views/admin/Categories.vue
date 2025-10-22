<template>
  <h2>Gestión de Categorías</h2>
  <div class="admin-categories">
    <h2>Agregar:</h2>

    <!-- 📝 Formulario de creación/edición -->
    <form class="category-form" @submit.prevent="saveCategory">
      <div class="grid">
        <div class="form-group">
          <label for="nombre_cat">Nombre</label>
          <input name="nombre_cat" v-model="form.name" required />
        </div>
        <div class="form-group">
          <label for="descript_cat">Descripción</label>
          <textarea name="descript_cat" v-model="form.description" rows="2"></textarea>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" :disabled="loading">{{ form.id ? 'Actualizar' : 'Crear' }}</button>
        <button v-if="form.id" type="button" class="cancel-btn" @click="resetForm" :disabled="loading">
          Cancelar
        </button>
      </div>
    </form>

    <!-- 🔍 Filtros -->
    <h3>Filtro de búsqueda</h3>
    <div class="filters">
      <input
        v-model="search"
        placeholder="Buscar categoría..."
        @input="() => loadCategories(1)"
      />
      <button @click="resetFilters" class="reset-btn" :disabled="loading">Limpiar</button>
    </div>
    <p v-if="meta.last_page">Total páginas: {{ meta.last_page }}</p>

    <!-- ⚠️ Estado -->
    <p v-if="loading">Cargando categorías...</p>
    <p v-if="error" class="error">{{ error }}</p>



    <!-- 📦 Listado -->
    <h3>Listado de Categorías</h3>
    <table v-if="!loading && categories.length" class="categories-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Productos Asociados</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cat in categories" :key="cat.id">
          <td>{{ cat.id }}</td>
          <td>{{ cat.name }}</td>
          <td>{{ cat.description || '—' }}</td>
          <td>{{ cat.products_count ?? 0 }}</td>
          <td>
            <button @click="editCategory(cat)">✏️</button>
            <button @click="deleteCategory(cat.id)">🗑️</button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-else-if="!loading">No hay categorías registradas.</p>

    <!-- 📄 Paginación -->
    <div class="pagination" v-if="meta.last_page > 1">
      <button
        :disabled="meta.current_page === 1 ?? loading"
        @click="changePage(meta.current_page - 1)"
      >
        ⬅️ Anterior
      </button>
      <span>Página {{ meta.current_page }} de {{ meta.last_page }}</span>
      <button
        :disabled="meta.current_page === meta.last_page ?? loading"
        @click="changePage(meta.current_page + 1)"
      >
        Siguiente ➡️
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

// Estado reactivo
const categories = ref([])
const form = ref({
  id: null,
  name: '',
  description: '',
})
const search = ref('')
const meta = ref({ current_page: 1, last_page: 1 })
const loading = ref(false)
const error = ref(null)

/** ✅ Cargar categorías con filtros y paginación */
async function loadCategories(page = 1) {
  try {
    loading.value = true
    error.value = null

    const params = {
      page,
      per_page: 10,
      ...(search.value ? { search: search.value } : {}),
    }

    const { data } = await api.get('/categories', { params })

    // Si el backend usa paginate():
    categories.value = data.data ?? data
    meta.value = {
      current_page: data.current_page ?? 1,
      last_page: data.last_page ?? 1,
    }
  } catch (err) {
    console.error('Error al cargar categorías:', err)
    error.value = 'No se pudieron cargar las categorías'
  } finally {
    loading.value = false
  }
}

/** ✅ Crear o actualizar categoría */
async function saveCategory() {
  try {
    loading.value = true
    const payload = {
      name: form.value.name,
      description: form.value.description,
    }



    if (form.value.id) {
      await api.put(`/categories/${form.value.id}`, payload)
      alert('Categoría actualizada')
    } else {
      await api.post('/categories', payload)
      alert('Categoría creada')
    }

    await loadCategories()
    resetForm()
  } catch (err) {
    console.error('Error al guardar categoría:', err)
    alert('No se pudo guardar la categoría')
  } finally {
    loading.value = false
  }
}

/** ✅ Editar categoría */
function editCategory(cat) {
  form.value = { ...cat }
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

/** ✅ Eliminar categoría */
async function deleteCategory(id) {
  if (!confirm('¿Seguro que deseas eliminar esta categoría?')) return
  try {
    loading.value = true
    await api.delete(`/categories/${id}`)
    await loadCategories()
  } catch (err) {
    console.error('Error eliminando categoría:', err)
    alert('No se pudo eliminar la categoría')
  } finally {
    loading.value = false
  }
}

/** ✅ Limpiar formulario */
function resetForm() {
  form.value = { id: null, name: '', description: '' }
}

/** ✅ Cambiar página */
function changePage(page) {
  loadCategories(page)
}

/** ✅ Resetear filtros */
function resetFilters() {
  search.value = ''
  loadCategories(1)
}

/** ✅ Inicialización */
onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.admin-categories {
  padding: 2rem;
  max-width: 1000px;
  margin: 0 auto;
  color: #fff;
}

.category-form {
  background: #2b2b2b;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.filters input {
  padding: 0.5rem;
  border-radius: 6px;
  border: 1px solid #555;
  background: #1c1c1c;
  color: #fff;
}

.reset-btn {
  background: #444;
  border: none;
  padding: 0.5rem 1rem;
  color: #fff;
  border-radius: 6px;
  cursor: pointer;
}
.reset-btn:hover {
  background: #666;
}

.form-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 0.25rem;
}

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
}

button[type="submit"] {
  background: var(--color-accent);
  color: #000;
  font-weight: 600;
  border: none;
  border-radius: 6px;
  padding: 0.5rem 1rem;
}
.cancel-btn {
  background: #555;
  color: #fff;
}

.categories-table {
  width: 100%;
  border-collapse: collapse;
  background: #1e1e1e;
  border-radius: 8px;
  overflow: hidden;
}

.categories-table th,
.categories-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #333;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 1rem;
}

.pagination button {
  padding: 0.4rem 0.8rem;
  background: #333;
  border: none;
  color: #fff;
  border-radius: 4px;
  cursor: pointer;
}
.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.error {
  color: #f66;
  background: #331;
  padding: 0.5rem;
  border-radius: 6px;
  margin-bottom: 1rem;
}
</style>
