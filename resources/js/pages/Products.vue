<script setup>
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import api from '../services/api';

    const products = ref([]);
    const pagination_data = ref([]);
    const page = ref(1);
    const pagintaion_size = ref(10);
    const router = useRouter();
    const loading = ref(false);
    const sort_field = ref('');
    const sort_direction = ref('');

    async function getProducts(page_number = page.value, page_size = pagintaion_size.value) {
        loading.value = true;
        try {
            const { data } = await api.get('/products',{
                params:{
                    page: page_number,
                    pagintaion_size: page_size,
                    sort_field: sort_field.value,
                    sort_direction: sort_direction.value,
                }
            });
            products.value = data.data;
            pagination_data.value = data;
            loading.value = false;
            
        } catch (e) {
            console.log(e);
            
            /* if (e.response?.status) {
                // localStorage.removeItem('token');
            } else {
                e.value = 'Ошибка загрузки пресетов';
            } */
            
        }
        
    }

    onMounted(async () => {
        if (localStorage.getItem('token')) {
            await getProducts();
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
                        <th class="border p-1 w-[25%]">Название</th>
                        <th class="border p-1 w-[10%]">Цена товара</th>
                        <th class="border p-1 w-[55%]">Состав</th>
                        <th class="border p-1 w-[10%]">Связанный пресет</th>
                    </tr>
                </thead>
                <tbody v-for="product in products" :key="product.id">
                    <tr>
                        <td class="border p-1">#{{ product.id }}. {{ product.name }}</td>
                        <td class="border p-1">{{ product.price }}</td>
                        <td class="border p-1">{{ product.composition }}</td>
                        <td class="border p-1">{{ product.preset_id }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>