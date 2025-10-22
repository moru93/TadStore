<template>
  <aside :class="['sidebar', { 'sidebar-collapsed': collapsed }]">
    <div class="sidebar-header">
      <h2 v-if="!collapsed">Admin</h2>
      <button class="toggle-btn" @click="$emit('toggle')">
        <span v-if="collapsed">☰</span>
        <span v-else>×</span>
      </button>
    </div>

    <nav class="menu">
      <router-link to="/admin/dashboard" class="menu-item" active-class="active">
        <i class="fas fa-chart-line"></i>
        <span v-if="!collapsed">Dashboard</span>
      </router-link>
      <router-link to="/admin/products" class="menu-item" active-class="active">
        <i class="fas fa-box"></i>
        <span v-if="!collapsed">Products</span>
      </router-link>
      <router-link to="/admin/categories" class="menu-item" active-class="active">
        <i class="fas fa-tags"></i>
        <span v-if="!collapsed">Categories</span>
      </router-link>
      <router-link to="/admin/users" class="menu-item" active-class="active">
        <i class="fas fa-users"></i>
        <span v-if="!collapsed">Users</span>
      </router-link>
      <router-link to="/admin/orders" class="menu-item" active-class="active">
        <i class="fas fa-shopping-cart"></i>
        <span v-if="!collapsed">Orders</span>
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
defineProps({
  collapsed: {
    type: Boolean,
    default: false,
  },
});
</script>

<style scoped>
.sidebar {
  width: 240px;
  background-color: #1e293b;
  color: #f1f5f9;
  display: flex;
  flex-direction: column;
  transition: width 0.3s ease;
}

.sidebar-collapsed {
  width: 70px;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #334155;
}

.toggle-btn {
  background: none;
  border: none;
  color: #f1f5f9;
  font-size: 1.2rem;
  cursor: pointer;
}

.menu {
  flex: 1;
  padding: 1rem 0;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1.25rem;
  text-decoration: none;
  color: #cbd5e1;
  font-size: 0.95rem;
  transition: background 0.2s;
}

.menu-item i {
  margin-right: 1rem;
}

.menu-item:hover {
  background-color: #334155;
}

.menu-item.active {
  background-color: #475569;
  color: #fff;
}
</style>
