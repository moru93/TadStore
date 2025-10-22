<template>
  <div class="login-container">
    <h2>Iniciar sesión</h2>
    <input v-model="email" placeholder="Email" />
    <input v-model="password" type="password" placeholder="Contraseña" />
    <button @click="handleLogin">Entrar</button>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '@/stores/user';
import { login } from '../services/auth';

const email = ref('');
const password = ref('');
const router = useRouter();
const userStore = useUserStore(); // ✅ importa el store

async function handleLogin() {
  try {
    const user = await login(email.value, password.value);

    // ✅ Usa el método del store
    userStore.setUser(user);

    // ✅ Redirección según el rol
    if (user.role === 'admin') {
      router.push('/admin/dashboard');
    } else if (user.role === 'cliente') {
      router.push('/cliente/dashboard');
    } else {
      router.push('/');
    }
  } catch (e) {
    console.error('Login error:', e);
    alert('Error al iniciar sesión: ' + (e?.message || 'Intente nuevamente.'));
  }
}
</script>
