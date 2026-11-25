<script setup>
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import api from '../services/api';

    const products = ref([]);
    const page = ref(1);
    const pagintaion_size = ref(10);
    const router = useRouter();
    const loading = ref(false);

    onMounted(async () => {
        if (localStorage.getItem('token')) {
            await getUser();
        }
    });
</script>

<template>
    <div class="main">
        <h2 class="mb-4 font-semibold">Список продуктов</h2>
        <div class="">
            <table id="products-table" class="shadow-xs w-full">
                <thead>
                    <tr>
                        <th class="border p-1">Название</th>
                        <th class="border p-1">Цена товара</th>
                        <th class="border p-1">Состав</th>
                        <th class="border p-1">Связанный пресет</th>
                    </tr>
                </thead>
                <tbody v-for="product in products" :key="product.id">
                    <tr>
                        <td class="border p-1"></td>
                        <td class="border p-1"></td>
                        <td class="border p-1"></td>
                        <td class="border p-1"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>