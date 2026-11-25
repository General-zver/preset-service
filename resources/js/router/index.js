import { createRouter, createWebHistory } from 'vue-router';

import Home from '../pages/Home.vue';
import Products from '../pages/Products.vue';
import Presets from '../pages/Presets.vue';
import Login from '../pages/Login.vue';


export default createRouter({
    history: createWebHistory(),
    routes: [
        {path: '/home', component: Home},
        {path: '/products', component: Products},
        {path: '/presets', component: Presets},
        {path: '/login', component: Login}
    ],
});