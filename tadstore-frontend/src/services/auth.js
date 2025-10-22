import api from './api';

export async function login(email, password) {
  const { data } = await api.post('/login', { email, password });
  localStorage.setItem('token', data.access_token);
  return { ...data.user, token: data.access_token };
}

export async function register(name, email, password, password_confirmation) {
  const { data } = await api.post('/register', {
    name, email, password, password_confirmation
  });
  return data;
}

export function logout() {
  localStorage.removeItem('token');
    localStorage.removeItem('user');
}
