<script setup>
import { ref, watchEffect } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { onMounted } from 'vue';

const page = usePage();
const show = ref(true);
const style = ref('success');
const message = ref('');

const props = defineProps({
    usuario: { type: Object }
});

const tipo_usuario = ref('');

const toggleSidebar = () => {
    sideNav.classList.toggle('hidden'); // Agrega o quita la clase 'hidden' para mostrar u ocultar la navegación lateral
}

watchEffect(async () => {
    style.value = page.props.jetstream?.flash?.bannerStyle || 'success';
    message.value = page.props.jetstream?.flash?.banner || '';
    show.value = true;
});

onMounted(async () => {
    try {
        const usuario = await axios.get(route('obtenerUsuario'));
        const idTipoUsuario = usuario.data.idTipoUsuario;

        const tipoUsuario = await axios.get(route('obtenerTipoUsuario', idTipoUsuario));
        const datosTipoUsuario = tipoUsuario.data.tipoUsuario;
        tipo_usuario.value = datosTipoUsuario;

    } catch (e) {
        tipo_usuario.value = "Sin asignar";
        console.log("Error: " + e);
    }
});
</script>

<template>
    <div class="bg-cyan-500 text-white shadow w-full flex items-center justify-between md:max-h-12 h-24 rounded-sm">
        <div class="flex items-center h-full">
            <div class="md:hidden flex items-center pe-2 mx-1"> <!-- Se muestra solo en dispositivos pequeños -->
                <button id="menuBtn" @click="toggleSidebar">
                    <i class="fas fa-bars text-white text-lg"></i> <!-- Ícono de menú -->
                </button>
            </div>
            <div class="h-full">
                <img :src="'https://i.postimg.cc/dh2vL0LL/Logo-Choferes-Del-Sur-SF.png'"
                    class="custom-img-size border-2 border-cyan-500 object-cover rounded-lg">
            </div>


        </div>
        <div class="flex items-center h-full">
            <div class="md:flex items-center justify-center text-center hidden">
                <!-- Mostrado en todos los dispositivos -->
                <h1 class="md:font-bold md:text-5xl font-semibold text-4x"> Choferes del Sur </h1>
            </div>
            <div class="flex items-center justify-center text-center md:hidden">
                <!-- Mostrado en todos los dispositivos -->
                <h1 class="md:font-bold md:text-2xl font-semibold text-lg">Choferes del sur</h1>
            </div>
        </div>
        <div class="flex items-center h-full">
            <div class="flex items-center text-center h-full justify-center mx-1">
                <div class="flex items-center text-center h-full justify-center mx-1">
                    <i class="fas fa-user text-white font-thin mx-1"></i>
                    <i class="text-white font-['DM Sans'] mx-1"> {{ " " +
                        props.usuario.tipoUsuario2.charAt(0).toUpperCase() + props.usuario.tipoUsuario2.slice(1) }} </i>
                </div>
                <!-- <div class="h-full">
                    <img :src="'https://i.postimg.cc/gwGpRPZ4/Logo-Tucdosa-SF.png'"
                        class="md:h-full h-14 md:mx-1 border-2 border-cyan-500 object-cover rounded-lg">
                </div> -->
            </div>
        </div>

    </div>
</template>

<style>
.custom-img-size {
    width: 100px;
    /* Ajusta a tu tamaño deseado */
    height: auto;
    /* Mantiene la proporción de la imagen */
}

@media (min-width: 768px) {
    .custom-img-size {
        width: 200px;
        /* Ajusta a tu tamaño deseado para pantallas medianas y grandes */
    }
}

</style>