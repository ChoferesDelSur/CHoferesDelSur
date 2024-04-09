<script setup>
import { ref, watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(true);
const style = ref('success');
const message = ref('');

const props = defineProps({
    usuario: { type: Object }
});

const toggleSidebar = () => {
    sideNav.classList.toggle('hidden'); // Agrega o quita la clase 'hidden' para mostrar u ocultar la navegación lateral
}

watchEffect(async () => {
    style.value = page.props.jetstream.flash?.bannerStyle || 'success';
    message.value = page.props.jetstream.flash?.banner || '';
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
        </div>
</div>
</template>