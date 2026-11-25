<script setup>
import { ref, onMounted } from 'vue';
import api from './services/api';

const email = ref('');
const password = ref('');
const user = ref(null);
const error = ref('');
const loading = ref(false);

async function login() {
    loading.value = true;
    error.value = '';

    try {
        const { data } = await api.post('/login', {
            email: email.value,
            password: password.value,
        });

        localStorage.setItem('token', data.token);
        password.value = '';

        await getUser();
    } catch (e) {
        error.value = e.response?.data?.message
            || 'Ошибка авторизации';
    } finally {
        loading.value = false;
    }
}

async function getUser() {
    try {
        const { data } = await api.get('/user');
        user.value = data;
    } catch (e) {
        if (e.response?.status === 401) {
            localStorage.removeItem('token');
            user.value = null;
        } else {
            error.value = 'Ошибка загрузки пользователя';
        }
    }
}

async function logout() {
    try {
        await api.post('/logout');
    } finally {
        localStorage.removeItem('token');
        user.value = null;
    }
}

onMounted(async () => {
    if (localStorage.getItem('token')) {
        await getUser();
    }
});
</script>

<template>
    <div class="main">
        <p v-if="error">{{error}}</p>

        <div v-if="user" class="">
            <div class="">{{user.name}}</div>
        </div>
        <form v-else @submit.prevent="login" class="auth-form">
            <h1 class="text-center">Авторизация</h1>
            <input v-model="email" type="email" placeholder="Почта" required>
            <input v-model="password" type="password" placeholder="Пароль" required>
            <button type="submit">{{ loading ? "Вход" : "Войти" }}</button>
        </form>
    </div>
</template>
<style>
    body{
        background: linear-gradient(180deg, #b1d2d3 50%, transparent);
        background-repeat: no-repeat;
        height: 100vh;
    }
    body *{
        font-family: Trebuchet MS;
    }
    .main{
        padding: 2rem;
    }
    h1{
        font-size: 24px;
    }
    .auth-form{
        display: flex;
        max-width: 780px;
        margin: auto;
        flex-direction:column;
    }
    .auth-form input{
        margin-bottom: 12px;
        padding: 8px 12px;
        border: 1px solid #bcbcbc;
        border-radius: 4px;
        font-size: 14px;
    }
    .auth-form button{
        padding: 8px 12px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 4px;
        border: 1px solid #bcbcbc;
    }
    .auth-form button:hover{
        cursor: pointer;
        background:rgb(218, 215, 215);
    }
    .text-center{
        text-align: center;
    }
</style>