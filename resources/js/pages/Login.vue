<script setup>

    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import api from '../services/api';

    const router = useRouter();

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
        loading.value = true;
        try {
            const { data } = await api.get('/user');
            user.value = data;
            loading.value = false;
            if (user.value && localStorage.getItem('token')) {
                router.replace('/home');
            }
        } catch (e) {
            if (e.response?.status === 401) {
                localStorage.removeItem('token');
                user.value = null;
            } else {
                error.value = 'Ошибка загрузки пользователя';
            }
            loading.value = false;
        }
    }
</script>

<template>
    <div class="main">
        <p v-if="error">{{error}}</p>
        <form @submit.prevent="login" class="auth-form">
            <h1 class="text-center">Авторизация</h1>
            <input v-model="email" type="email" placeholder="Почта" required>
            <input v-model="password" type="password" placeholder="Пароль" required>
            <button type="submit">{{ loading ? "Вход" : "Войти" }}</button>
        </form>
    </div>
</template>