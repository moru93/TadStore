import api from './api';

export const getProducts = () => api.get('/products');
export const getCategories = () => api.get('/categories');
export const createOrder = (orderData) => api.post('/orders', orderData);
export const getOrders = () => api.get('/orders');
export const login = (loginData) => api.post('/login', loginData);
