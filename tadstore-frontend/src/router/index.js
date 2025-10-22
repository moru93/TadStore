import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Products from '../views/Products.vue'
import Login from "../views/Login.vue";

const routes = [
  {
    path: '/', 
    name: 'Home',
    component: Home,
  },
  { path: "/login", name: "Login", component: Login },
  {
    path: '/products',
    name: 'Products',
    component: Products,
  },
  {
    path: '/admin/dashboard',
    component: () => import('../views/admin/Dashboard.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/products',
    name: 'AdminProducts',
    component: () => import('@/views/admin/Products.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/categories',
    name: 'AdminCategories',
    component: () => import('@/views/admin/Categories.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/orders',
    name: 'AdminOrders',
    component: () => import('@/views/admin/Orders.vue'),
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Middleware para proteger rutas
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");
  const user = JSON.parse(localStorage.getItem("user"));
  if (to.meta.requiresAuth && !token && !user) {
    next("/login");
  } else if (to.meta.role && user?.role !== to.meta.role) {
    next("/forbidden");
  } else {
    next();
  }
});

export default router;
