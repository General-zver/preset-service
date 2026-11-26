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
    const filter_params = ref([]);

    const error = ref('');

    async function getPresets(page_number = page.value, page_size = pagintaion_size.value) {
        loading.value = true;
        try {
            const { data } = await api.get('/presets',{
                params:{
                    page: page_number,
                    pagintaion_size: page_size,
                    sort_field: sort_field.value,
                    sort_direction: sort_direction.value,
                    filters: filter_params.value,
                },
            });
            presets.value = data.data;
            pagination_data.value = data;
            loading.value = false;
            
        } catch (e) {
            if (e.response?.status) {
                // localStorage.removeItem('token');
                error.value = e.response?.message ?? 'Ошибка загрузки пресетов';
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
    const setFilter = async (e, field) => {
        const value = e.target.value;
        const index = filter_params.value.findIndex(
            filter => filter.field === field
        );

        if (value === '') {
            if (index !== -1) {
                filter_params.value.splice(index, 1);
            }
        } else {
            const rule = {
                field,
                value,
            };

            if (index === -1) {
                filter_params.value.push(rule);
            } else {
                filter_params.value[index] = rule;
            }
        }

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
            <select class="border py-1 px-2 rounded" @change="setFilter($event,'status')">
                <option value="">Все</option>
                <option value="0">Доступно</option>
                <option value="1">Забронировано</option>
            </select>
        </div>
        <div class="">
            <table id="presets-table" class="shadow-xs w-full">
                <thead>
                    <tr>
                        <th class="w-[25%] border p-1 w">Название</th>
                        <th class="w-[15%] border p-1 cursor-pointer" @click="setSortParams($event,'status', sort_direction)">
                            Статус
                            <span v-if="sort_field=='status' && sort_direction=='desc'">&#9660;</span>
                            <span v-if="sort_field=='status' && sort_direction=='asc'">&#9650;</span>
                        </th>
                        <th class="w-[15%] border p-1 cursor-pointer" @click="setSortParams($event,'products_count', sort_direction)">
                            Количество товаров
                            <span v-if="sort_field=='products_count' && sort_direction=='desc'">&#9660;</span>
                            <span v-if="sort_field=='products_count' && sort_direction=='asc'">&#9650;</span>
                        </th>
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
                            <button v-if="!preset.status" class="w-full p-1 cursor-pointer" @click="">Забронировать</button>
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
