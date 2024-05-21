<script setup>
import Mensaje from '../../Components/Mensaje.vue';
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
//import { defineProps } from 'vue';

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
});
</script>

<template>
    <PrincipalLayout title="Inicio">
        <div class="mt-8 bg-white p-4 shadow rounded-lg h-5/6">

            <Mensaje/>

            <h1 class="font-bold text-center text-xl pt-5"> Bienvenido al sistema de la empresa Choferes del Sur</h1>
            
        </div>
    </PrincipalLayout>
</template>
<style>
.text-lgg {
    font-size: 1.190rem
        /* 20px */
    ;
    line-height: 1.75rem
        /* 28px */
    ;
}
</style>