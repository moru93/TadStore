<template>
  <h2>Gestión de Productos</h2>
  <div class="admin-products">
    <h2>Agregar:</h2>

    <!-- 📝 Formulario de creación/edición -->
    <form class="product-form" @submit.prevent="saveProduct">
      <div class="grid">
        <div class="form-group">
          <label>Nombre</label>
          <input v-model="form.name" required />
        </div>

        <div class="form-group">
          <label>Precio</label>
          <input v-model.number="form.price" type="number" min="0" step="0.01" required />
        </div>

        <div class="form-group">
          <label>Stock</label>
          <input v-model.number="form.stock" type="number" min="0" />
        </div>

        <div class="form-group">
          <label>Categoría</label>
          <select v-model="form.category_id" :disabled="!categories.length" required>
            <option value="">Seleccione una</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>
      </div>

      <label>Descripción</label>
      <textarea v-model="form.description" rows="2"></textarea>

      <label for="image_url">Imagen</label>
      <input name="image_url" placeholder=".jpg, .png, .jpeg" type="file" @change="onFileChange" />

      <div class="form-actions">
        <button type="submit">{{ form.id ? 'Actualizar' : 'Crear' }}</button>
        <button v-if="form.id" type="button" class="cancel-btn" @click="resetForm">
          Cancelar
        </button>
      </div>
    </form>

    <!-- 🔍 Filtros -->
    <h3>Filtro de búsqueda</h3>
    <div class="filters">
        <input v-model="search" placeholder="Buscar por nombre..." @input="() => loadProducts(1)" />
        <select v-model="filterCategory" @change="() => loadProducts(1)">

        <option value="">Todas las categorías</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
          {{ cat.name }}
        </option>
      </select>
      <button @click="resetFilters" class="reset-btn">Limpiar</button>
    </div>
    <p v-if="meta.last_page">Total páginas: {{ meta.last_page }}</p>

    <p v-if="loading">Cargando productos...</p>
    <p v-else-if="!products.length">No hay productos registrados.</p>


    <!-- 📦 Listado -->
    <h3>Listado de Productos</h3>
    <table class="products-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in products" :key="p.id">
          <td>{{ p.id }}</td>
          <td>
            <img
              v-if="p.image_url"
              :src="fixImagePath(p.image_url)"
              alt="Producto"
              class="thumb"
            />


            <span v-else>—</span>
          </td>
          <td>{{ p.name }}</td>
          <td>{{ getCategoryName(p.category_id) }}</td>
          <td>${{ Number(p.price).toFixed(2) }}</td>
          <td>{{ p.stock }}</td>
          <td>
            <button @click="editProduct(p)">✏️</button>
            <button @click="deleteProduct(p.id)">🗑️</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- 📄 Paginación -->
    <div class="pagination" v-if="meta.last_page > 1">
      <button :disabled="meta.current_page === 1" @click="changePage(meta.current_page - 1)">
        ⬅️ Anterior
      </button>
      <span>Página {{ meta.current_page }} de {{ meta.last_page }}</span>
      <button :disabled="meta.current_page === meta.last_page" @click="changePage(meta.current_page + 1)">
        Siguiente ➡️
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const products = ref([])
const categories = ref([])
const form = ref({
  id: null,
  name: '',
  description: '',
  price: 0,
  stock: 0,
  category_id: '',
  image_url: '',
})

const search = ref('')
const filterCategory = ref('')
const meta = ref({ current_page: 1, last_page: 1 })
const imageFile = ref(null)
const loading = ref(false)
const error = ref(null)


// ✅ Cargar productos con paginación y filtros
async function loadProducts(page = 1) {
  try {
    loading.value = true
    error.value = null

    const params = {
      page,
      per_page: 10,
      ...(search.value ? { search: search.value } : {}),
      ...(filterCategory.value ? { category_id: filterCategory.value } : {}),
    }

    const { data } = await api.get('/products', { params })

    products.value = data.data ?? []
    meta.value = {
      current_page: data.current_page ?? 1,
      last_page: data.last_page ?? 1,
    }

  } catch (err) {
    console.error('Error al cargar productos:', err)
    error.value = 'No se pudieron cargar los productos'
  } finally {
    loading.value = false
  }
}

// ✅ Cargar categorías correctamente (aunque el backend devuelva paginación)
async function loadCategories() {
  try {
    const { data } = await api.get('/categories', { params: { per_page: 100 } })
    categories.value = data.data ?? data // si tiene paginación, toma data.data
  } catch (err) {
    console.error('Error al cargar categorías:', err)
    categories.value = []
  }
}

// ✅ Crear o actualizar producto
async function saveProduct() {
  try {
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    formData.append('price', form.value.price);
    formData.append('stock', form.value.stock);
    formData.append('category_id', form.value.category_id);

    if (imageFile.value) {
      formData.append('image_url', imageFile.value); // Laravel lo espera como 'image_url'
    }

    let res;
    if (form.value.id) {
      res = await api.post(`/products/${form.value.id}?_method=PUT`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      alert('Producto actualizado');
    } else {
      res = await api.post('/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      alert('Producto creado');
    }

    await loadProducts();
    resetForm();
  } catch (err) {
    console.error('Error al guardar producto:', err);
    alert('No se pudo guardar el producto');
  }
}


// ✅ Editar producto
function editProduct(p) {
  form.value = { ...p }
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// ✅ Eliminar producto
async function deleteProduct(id) {
  if (!confirm('¿Seguro que deseas eliminar este producto?')) return
  try {
    await api.delete(`/products/${id}`)
    await loadProducts()
  } catch (err) {
    console.error('Error eliminando producto:', err)
    alert('No se pudo eliminar el producto')
  }
}

// ✅ Obtener nombre de categoría
function getCategoryName(id) {
  if (!id || !Array.isArray(categories.value)) return '—'
  const cat = categories.value.find(c => Number(c.id) === Number(id))
  return cat ? cat.name : '—'
}


// ✅ Limpiar formulario
function resetForm() {
  form.value = {
    id: null,
    name: '',
    description: '',
    price: 0,
    stock: 0,
    category_id: '',
    image_url: '',
    preview_url: '',
  };
  imageFile.value = null;
  const input = document.querySelector('input[name="image_url"]');
  if (input) input.value = ''; // limpiar campo de archivo
}


// ✅ Cambiar página
function changePage(page) {
  loadProducts(page)
}

// ✅ Resetear filtros
function resetFilters() {
  search.value = ''
  filterCategory.value = ''
  loadProducts(1)
}


function fixImagePath(url) {
  if (!url) return "";

  // Si la ruta ya es completa (http...), la devolvemos
  if (url.startsWith("http")) return url;

  // Caso típico: el backend devuelve "/storage/products/imagen.png"
  return `http://127.0.0.1:8000${url}`;
}

onMounted(async () => {
  await loadCategories()
  await loadProducts()
})

function onFileChange(event) {
  const file = event.target.files[0];
  if (!file) return;

  const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
  if (!validTypes.includes(file.type)) {
    alert('Archivo no válido (.jpg, .jpeg, .png)');
    return;
  }

  imageFile.value = file;
  form.value.image_url = URL.createObjectURL(file); // preview local
}

</script>

<style scoped>
.admin-products {
  padding: 2rem;
  max-width: 1100px;
  margin: 0 auto;
  color: #fff;
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.filters input,
.filters select {
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

.product-form {
  background: #2b2b2b;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.form-group label {
  display: block;
  font-weight: 500;
  margin-bottom: 0.25rem;
}


.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

.products-table {
  width: 100%;
  border-collapse: collapse;
  background: #1e1e1e;
  border-radius: 8px;
  overflow: hidden;
}

.products-table th,
.products-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #333;
}

.thumb {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 6px;
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
</style>
