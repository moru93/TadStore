import { createPinia } from 'pinia'
import { createApp } from 'vue'
import './style/variables.css'
import axios from 'axios'
import App from './App.vue'
import router from './router'

axios.defaults.baseURL = 'http://127.0.0.1:8000/api'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.post['Content-Type'] = 'application/json'

axios.defaults.withCredentials = false

const token = localStorage.getItem('token');
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const app = createApp(App)
const pinia = createPinia();

app.use(pinia);
app.use(router)

app.mount('#app')



