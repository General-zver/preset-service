<script setup>
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import api from '../services/api';

    const router = useRouter();

    const user = ref(null);
    const error = ref('');
    const loading = ref(false);

    async function getUser() {
        loading.value = true;
        try {
            const { data } = await api.get('/user');
            user.value = data;
            loading.value = false;
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

    async function logout() {
        try {
            await api.post('/logout');
        } finally {
            localStorage.removeItem('token');
            user.value = null;
            router.replace('/login');
        }
    }

    onMounted(async () => {
        if (localStorage.getItem('token')) {
            await getUser();
        }else{
            router.replace('/login');
        }
    });
</script>

<template>
    <div class="main">
        <p v-if="error">{{error}}</p>

        <div v-if="user" class="">
            <div class="flex justify-between">
                <div class="mb-3 font-semibold text-lg">Привет, {{user.name}}</div>
                <button class="border px-2 py-1 rounded cursor-pointer" type="button" @click="logout">Выйти</button>
            </div>
            <div class="flex flex-col">
                <div class="">Список твоих возможностей:</div>
                <RouterLink to="/presets" class="underline">Список пресетов</RouterLink>
                <RouterLink to="/products" class="underline">Список товаров</RouterLink>
                <RouterLink to="/docs" class="underline">Документация API</RouterLink>
            </div>
        </div>
        
    </div>
</template>
