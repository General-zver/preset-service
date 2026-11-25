<script setup>
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import api from '../services/api';
    const router = useRouter();

    const presets = ref([]);
    const pagination_data = ref([]);
    const page = ref(1);
    const pagintaion_size = ref(10);
    const loading = ref(false);
    const sort_field = ref('');
    const sort_direction = ref('');

    const error = ref('');

    async function getPresets(page_number = page.value, page_size = pagintaion_size.value) {
        loading.value = true;
        try {
            
            const { data } = await api.post('/presets',{
                page: page_number,
                pagintaion_size: page_size,
                sort_field: sort_field.value,
                sort_direction: sort_direction.value,
            });
            presets.value = data.data;
            pagination_data.value = data;
            loading.value = false;
            const presets_table = document.getElementById('presets-table');
            
        } catch (e) {
            if (e.response?.status) {
                // localStorage.removeItem('token');
            } else {
                error.value = 'Ошибка загрузки пресетов';
            }
        }
        loading.value = false;
        
    }
    const test = async (e, page_number) =>  {
        console.log(e, page_number);
        await getPresets(page_number);
    }

    const setSortParams = async (e, field, sort_dir) => {
        if (sort_field.value == field) {
            sort_direction.value = sort_dir=='' ? 'asc' : (sort_dir=='asc' ? 'desc' : '');
        } else {
            sort_direction.value = 'asc';
        }
        sort_field.value = field;

        await getPresets();
    }

    onMounted(async () => {
        if (localStorage.getItem('token')) {
            await getPresets();
        }else{
            router.replace('/login');
        }
    });
</script>

<template>
    <div class="main">
        <h2 class="mb-4 font-semibold">Список пресетов</h2>
        <div class="my-4">
            <!-- <select class="border p-1" name="" id=""></select> -->
        </div>
        <div class="">
            <table id="presets-table" class="shadow-xs w-full">
                <thead>
                    <tr>
                        <th class="w-[25%] border p-1 w">Название</th>
                        <th class="w-[15%] border p-1" @click="setSortParams($event,'status', sort_direction)">
                            Статус
                            <span v-if="sort_field=='status' && sort_direction=='desc'">&#9660;</span>
                            <span v-if="sort_field=='status' && sort_direction=='asc'">&#9650;</span>
                        </th>
                        <th class="w-[15%] border p-1 cursor-pointer">Количество товаров</th>
                        <th class="w-[15%] border p-1 cursor-pointer" @click="setSortParams($event,'products_sum', sort_direction)">
                            Цена товаров
                            <span v-if="sort_field=='products_sum' && sort_direction=='desc'">&#9660;</span>
                            <span v-if="sort_field=='products_sum' && sort_direction=='asc'">&#9650;</span>
                        </th>
                        <th class="w-[15%] border p-1 cursor-pointer" @click="setSortParams($event,'preset_price', sort_direction)">
                            Цена присета
                            <span v-if="sort_field=='preset_price' && sort_direction=='desc'">&#9660;</span>
                            <span v-if="sort_field=='preset_price' && sort_direction=='asc'">&#9650;</span>
                        </th>
                        <th class="w-[10%] border p-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="preset in presets" :key="preset.id">
                        <td class="border p-1">#{{preset.id}}. {{preset.name}}</td>
                        <td class="border p-1">{{preset.status ? 'Забронировано' : "Доступно"}}</td>
                        <td class="border p-1">{{preset.products_count}}</td>
                        <td class="border p-1">{{preset.products_sum}}</td>
                        <td class="border p-1">{{preset.preset_price}}</td>
                        <td class="border">
                            <button v-if="!preset.status" class="w-full p-1 cursor-pointer">Забронировать</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pagination flex flex-row mt-2">
            <button type="button" class="border p-1 mx-1 cursor-pointer select-none"v-for="link in pagination_data.links"  @click="test($event, link.page)" v-html="link.label"></button>
        </div>
    </div>
</template>
