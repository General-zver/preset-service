<script setup>
    import { ref, onMounted } from 'vue';
    import { useRouter } from 'vue-router';
/* import api from './services/api'; */

    const loading = ref(false);
    const router = useRouter();

    onMounted(async () => {
        if (!localStorage.getItem('token')) {
            router.replace('/login');
        }
    });
</script>

<template>
    <div v-if="loading" class="preloader">
        <div class="loader"></div>
    </div>
    <div class="123">
        <RouterView />
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
    /* HTML: <div class="loader"></div> */
    .preloader{
        top:0;
        left: 0;
        position: absolute;
        height: 100%;
        width: 100%;
        background:rgba(0, 0, 0, 0.45);
    }
    .loader {
        position: absolute;
        top: calc(50% - 50px);
        left: calc(50% - 50px);
        width: 50px;
        padding: 8px;
        aspect-ratio: 1;
        border-radius: 50%;
        background: #25b09b;
        --_m: 
            conic-gradient(#0000 10%,#000),
            linear-gradient(#000 0 0) content-box;
        -webkit-mask: var(--_m);
                mask: var(--_m);
        -webkit-mask-composite: source-out;
                mask-composite: subtract;
        animation: l3 1s infinite linear;
    }
    @keyframes l3 {to{transform: rotate(1turn)}}
</style>