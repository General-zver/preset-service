import axios from 'axios';
import router from '../router/index.js';

const api = axios.create({
    baseURL: '/api',
    headers: {
        Accept: 'application/json',
    },
});

api.interceptors.request.use(config => {
    const token = localStorage.getItem('token');

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

api.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');

            if (router.currentRoute.value.path !== '/login') {
                router.replace('/login');
            }
        }

        return Promise.reject(error);
    }
);

export default api;