<script setup>
//import { defineProps } from 'vue';
import { ref, watchEffect} from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(true);
const style = ref('success');
const message = ref('');

const props = defineProps({
    //usuario: { type: Object }
});

const toggleSidebar = () => {
    sideNav.classList.toggle('hidden'); // Agrega o quita la clase 'hidden' para mostrar u ocultar la navegación lateral
}

/*const toggleSidebar = () => {
    const sideNav = document.getElementById('sideNav');
    if (sideNav) {
        sideNav.classList.toggle('hidden');
    }
}
*/

watchEffect(async () => {
    style.value = page.props.jetstream?.flash?.bannerStyle || 'success';
    message.value = page.props.jetstream?.flash?.banner || '';
    show.value = true;
});
</script>

<template>
    <div class="bg-cyan-600 text-white shadow w-full flex items-center justify-between md:max-h-12 h-24 rounded-sm">
        <div class="flex items-center h-full">
            <div class="md:hidden flex items-center pe-2 mx-1"> <!-- Se muestra solo en dispositivos pequeños -->
                <button id="menuBtn" @click="toggleSidebar">
                    <i class="fas fa-bars text-white text-lg"></i> <!-- Ícono de menú -->
                </button>
            </div>
            <div class="h-full">
                <img :src="'Imagenes/ChoferesDelSurLogo.png'" class="md:h-full h-14 md:mx-1 border-2 border-cyan-600 object-cover rounded-lg">
            </div>
            <div class="md:flex items-center justify-center text-center hidden">
                <!-- Mostrado en todos los dispositivos -->
                <h1 class="md:font-bold md:text-2xl font-semibold text-xl"> Choferes del sur</h1>
            </div>
        </div>
        <div class="flex items-center h-full">
            <div class="flex items-center text-center h-full justify-center mx-1">
                
            </div>        
        </div>
    </div>
</template>