<template>
  <section class="products-container">
    <h2 class="section-title">Nuestros Productos</h2>
    <div class="filters-bar">
      <CategoryFilter @change="handleCategoryChange" />
      <SearchBar @search="handleSearch" />
    </div>
    <!-- Estados -->
    <div v-if="loading" class="loading">Cargando productos...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else-if="!products.length" class="empty">
      No hay productos disponibles en este momento.
    </div>

    <!-- Grid de productos -->
    <div v-else class="products-grid">
      <div
        v-for="(product, index) in products"
        :key="product.id || index"
        class="product-card"
      >
        <img
          :src="resolveImageUrl(product.image_url) || defaultImage"
          :alt="product.name || 'Producto sin nombre'"
          class="product-image"
          loading="lazy"
        />

        <div class="product-info">
          <h3 class="product-name">{{ product.name || 'Producto sin nombre' }}</h3>
          <p class="product-description">
            {{ product.description || 'Sin descripción disponible.' }}
          </p>
          <p class="product-price">{{ formatCurrency(product.price || 0) }}</p>
          <p
            class="product-stock"
            :class="{ 'out-of-stock': product.stock === 0 }"
          >
            Stock: {{ product.stock > 0 ? product.stock : 'Agotado' }}
          </p>

          <button class="buy-button" :disabled="isOutOfStock(product)" @click="addToCart(product)"> {{ getButtonLabel(product) }} </button>
          <p v-if="getQuantityInCart(product.id) >= product.stock" class="stock-warning">
            Has alcanzado el máximo disponible.
          </p>
        </div>
      </div>
    </div>

    <!-- Sentinel para IntersectionObserver (invisible) -->
    <div ref="sentinel" class="sentinel" v-show="showSentinel"></div>

    <!-- Estado de carga extra (cuando el infinite scroll trae más) -->
    <div v-if="loadingMore" class="loading-more">Cargando más productos...</div>

    <!-- Fallback: botón "Cargar más" si existe nextPage (útil en dispositivos que no activan observer) -->
    <div class="pagination" v-if="!loading && nextPageUrl">
      <button class="load-more" @click="loadMore" :disabled="loadingMore">
        {{ loadingMore ? 'Cargando...' : 'Cargar más productos' }}
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import CategoryFilter from '@/components/CategoryFilter.vue'
import SearchBar from '@/components/SearchBar.vue'
import { useCart } from '@/composables/useCart'


const { items, addToCart } = useCart()
const selectedCategory = ref('')
const searchQuery = ref('')


function getQuantityInCart(productId) {
  const item = items.find((p) => p.id === productId);
  return item ? item.quantity : 0;
}

function isOutOfStock(product) {
  const qty = getQuantityInCart(product.id);
  return product.stock === 0 || qty >= product.stock;
}

function getButtonLabel(product) {
  const qty = getQuantityInCart(product.id);
  if (product.stock === 0) return "Producto agotado";
  if (qty >= product.stock) return "Sin stock disponible";
  return "🛒 Agregar al carrito";
}
const handleCategoryChange = async (categoryId) => {
  selectedCategory.value = categoryId
  await applyFilters()
}

const handleSearch = async (query) => {
  searchQuery.value = query
  await applyFilters()
}

const applyFilters = async () => {
  loading.value = true
  let allProducts = []

  try {
    // Si hay categoría seleccionada, obtenemos productos de esa categoría
    if (selectedCategory.value) {
      const { data } = await axios.get(`${API_BASE.replace('products', 'categories')}/${selectedCategory.value}`)
      allProducts = data.products || []
    } else {
      // Si no hay categoría, cargamos todos los productos
      const { data } = await axios.get(`${API_BASE}?per_page=${PER_PAGE}`)
      allProducts = Array.isArray(data.data) ? data.data : data
    }

    // Si hay texto de búsqueda, filtramos localmente
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      allProducts = allProducts.filter(
        (p) =>
          p.name?.toLowerCase().includes(query) ||
          p.description?.toLowerCase().includes(query)
      )
    }

    products.value = allProducts
    showSentinel.value = false
    nextPageUrl.value = null
  } catch (err) {
    console.error('Error aplicando filtros:', err)
    error.value = 'Error filtrando productos.'
  } finally {
    loading.value = false
  }
}
/**
 * Productos con infinite scroll (IntersectionObserver).
 * - Forzamos per_page = 8 en la primera petición para que con 10 productos haya
 *   una segunda página y el infinite scroll pueda demostrarse.
 * - Si el backend ignora per_page, todo seguirá funcionando (no se intenta cargar si next_page_url == null).
 */

// Config
const API_BASE = "http://127.0.0.1:8000/api/products";
const PER_PAGE = 8; // mostramos 8 por "página" para activar paginación en tu dataset de 10

// Reactivity
const products = ref([]);
const loading = ref(true);
const error = ref(null);
const loadingMore = ref(false);
const defaultImage = "https://placehold.co/300x200?text=Sin+imagen";


// Paginación
const currentPage = ref(1);
const lastPage = ref(1);
const nextPageUrl = ref(null);
const prevPageUrl = ref(null);

// Sentinel & observer
const sentinel = ref(null);
let observer = null;

// Mostrar sentinel: solo cuando exista nextPageUrl y ya cargó la primera página
const showSentinel = ref(false);

/**
 * fetchProducts(url, append)
 * - url: string (endpoint to fetch)
 * - append: boolean (if true, append results to products array)
 */
const fetchProducts = async (url = `${API_BASE}?per_page=${PER_PAGE}`, append = false) => {
  if (!url) return;
  try {
    if (append) loadingMore.value = true;
    else loading.value = true;

    const { data } = await axios.get(url);

    // Caso paginador Laravel: data.data (array) + data.next_page_url ...
    if (data && Array.isArray(data.data)) {
      if (append) products.value.push(...data.data);
      else products.value = data.data;

      currentPage.value = data.current_page ?? currentPage.value;
      lastPage.value = data.last_page ?? lastPage.value;
      nextPageUrl.value = data.next_page_url ?? null;
      prevPageUrl.value = data.prev_page_url ?? null;

      // mostrar sentinel si hay más páginas
      showSentinel.value = !!nextPageUrl.value;
    }
    // Caso array directo: data = [...]
    else if (Array.isArray(data)) {
      if (append) products.value.push(...data);
      else products.value = data;

      // No hay paginador → no next page
      nextPageUrl.value = null;
      showSentinel.value = false;
    }
    // Caso estructura { products: [...] }
    else if (Array.isArray(data.products)) {
      if (append) products.value.push(...data.products);
      else products.value = data.products;

      nextPageUrl.value = null;
      showSentinel.value = false;
    } else {
      // Formato inesperado: limpiar
      products.value = [];
      nextPageUrl.value = null;
      showSentinel.value = false;
      console.warn("Formato de respuesta inesperado:", data);
    }
  } catch (err) {
    console.error("Error cargando productos:", err);
    error.value = "No se pudieron cargar los productos. Intenta nuevamente.";
  } finally {
    loading.value = false;
    loadingMore.value = false;
  }
};

/** loadMore: carga la siguiente página usando nextPageUrl */
const loadMore = async () => {
  if (!nextPageUrl.value || loadingMore.value) return;
  await fetchProducts(nextPageUrl.value, true);

  // si ya no hay más páginas, desconectar observer y esconder sentinel
  if (!nextPageUrl.value) {
    disconnectObserver();
    showSentinel.value = false;
  }
};

/** Setup del IntersectionObserver */
const setupObserver = () => {
  if (!("IntersectionObserver" in window)) {
    // Si no existe IntersectionObserver (browsers muy viejos), fallback al boton (ya existe).
    return;
  }

  // Disconnect si ya existe uno
  if (observer) observer.disconnect();

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && nextPageUrl.value && !loadingMore.value) {
          // pequeña protección contra múltiples triggers
          loadMore();
        }
      });
    },
    {
      root: null,
      rootMargin: "200px", // comenzar a cargar antes de que aparezca totalmente
      threshold: 0.1,
    }
  );

  // observar el sentinel cuando esté disponible
  if (sentinel.value) observer.observe(sentinel.value);
};

const disconnectObserver = () => {
  if (observer) {
    observer.disconnect();
    observer = null;
  }
};

// Montaje
onMounted(async () => {
  // Cargamos la primera página (con per_page=8 para forzar paginación en tu dataset)
  await fetchProducts(`${API_BASE}?per_page=${PER_PAGE}`, false);

  // Si hay más páginas, mostramos el sentinel y configuramos el observer
  if (nextPageUrl.value) {
    showSentinel.value = true;
    // Delay pequeño para asegurar que sentinel está en DOM
    setTimeout(() => setupObserver(), 50);
  }
});

// Limpiar observer al desmontar
onBeforeUnmount(() => {
  disconnectObserver();
});

// Formateo de moneda
const formatCurrency = (value) =>
  new Intl.NumberFormat("es-CO", {
    style: "currency",
    currency: "COP",
    maximumFractionDigits: 0,
  }).format(value || 0);

const resolveImageUrl = (path) => {
  if (!path) return defaultImage;
  // Si ya es una URL completa (empieza con http o https), úsala tal cual
  if (/^https?:\/\//i.test(path)) return path;
  // Si es una ruta relativa (por ejemplo "storage/imagenes/x.png")
  return `http://127.0.0.1:8000/${path}`;
};

</script>

<style>
/* --- Mantengo tu CSS responsive original --- */
.products-container {
  max-width: 90vw;
  margin: 0 auto;
  padding: 2rem 1.5rem;
  color: var(--color-text);
}

/* Título */
.section-title {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 2rem;
  color: var(--color-accent);
  font-weight: 600;
  letter-spacing: 0.5px;
}

/* Estados */
.loading,
.error,
.empty,
.loading-more {
  text-align: center;
  font-size: 1.1rem;
  color: var(--color-muted);
  margin-top: 1.5rem;
}

/* Grid */
.products-grid {
  display: grid;
  gap: 2rem;
  justify-items: center;
  align-items: start;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  transition: all 0.3s ease;
}

/* Tarjeta */
.product-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 12px;
  overflow: hidden;
  width: 100%;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
  transition: transform 0.25s ease, box-shadow 0.25s ease,
    border-color 0.25s ease;
}

.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
  border-color: var(--color-border);
}

/* Imagen */
.product-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  background-color: #333;
}

/* Info */
.product-info {
  background-color: #e0dede;
  padding: 1rem;
  text-align: left;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.product-name {
  font-size: 1.1rem;
  color: var(--color-titleprod);
  margin-bottom: 0.4rem;
  font-weight: 600;
}

.product-description {
  font-size: 0.9rem;
  color: var(--color-border);
  margin-bottom: auto;
  line-height: 1.4;
  min-height: 40px;
}

.product-price {
  font-size: 1.1rem;
  color: var(--color-bg);
  margin: 0.8rem 0;
  font-weight: 700;
}

.product-stock {
  font-size: 0.9rem;
  margin-bottom: 0.6rem;
  color: var(--color-bg);
}

.product-stock.out-of-stock {
  color: #ff5c5c;
  font-weight: bold;
}

/* Botón */
.buy-button {
  /* background-color: transparent; */
  /* background: var(--color-); */

  border: 1px solid var(--color-surface);
  color: var(--color-surface);
  padding: 0.6rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.buy-button:hover {
  background: var(--color-bg);
  color: var(--color-text);
}

.buy-button:disabled {
  background: #555;
  color: #bbb;
  cursor: not-allowed;
  opacity: 0.7;
}

/* Sentinel (invisible pero ocupa espacio pequeño) */
.sentinel {
  width: 100%;
  height: 1px;
  margin-top: 8px;
  visibility: hidden;
}

/* Paginación / Load more button */
.pagination {
  display: flex;
  justify-content: center;
  margin-top: 1.5rem;
}

.load-more {
  background: transparent;
  border: 1px solid var(--color-accent);
  color: var(--color-accent);
  padding: 0.8rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.load-more:hover:not(:disabled) {
  background-color: var(--color-accent);
  color: var(--color-bg);
}

.load-more:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Responsive */
@media (min-width: 1400px) {
  .products-container {
    max-width: 1600px;
  }
  .products-grid {
    grid-template-columns: repeat(5, 1fr);
  }
}
@media (max-width: 1399px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
@media (max-width: 1024px) {
  .products-grid {
    gap: 1.5rem;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  }
}
@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 500px) {
  .products-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  .product-image {
    height: 180px;
  }
  .product-info {
    padding: 0.8rem;
  }
  .product-name {
    font-size: 1rem;
  }
}
.filters-bar {
  /* background: #c2b9b9; */
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 2rem;
  border-radius: 4px;
}

.stock-warning {
  font-size: 0.8rem;
  color: #ff5c5c;
  font-weight: 600;
  margin-top: 4px;
  text-align: center;
}

</style>
