<script setup>
import Mensaje from '../../Components/Mensaje.vue';
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
});

const generarArchivo = (tipo) => {
    // Lógica para generar el archivo según el tipo seleccionado
    Swal.fire({
        title: `Generar el reporte "${tipo}"`,
        text: `Aquí iría la lógica para generar el archivo ${tipo}`,
        icon: 'info',
        confirmButtonText: 'OK'
    });
};

</script>

<template>
    <PrincipalLayout title="Reportes">
        <div class="mt-1 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0">Reportes</h2>
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-3"></div>
            <Mensaje />

            <div class="mb-4 bg-zinc-100 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-2"> Cortes semanal</h3>
                <div class="flex flex-wrap space-x-3">
                    <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Cortes Semanal', 'pdf')">
                        <i class="fa-solid fa-file-pdf mr-2 jump-icon"></i> Generar PDF
                    </button>
                    <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Cortes Semanal', 'excel')">
                        <i class="fa-solid fa-file-excel mr-2 jump-icon"></i> Generar Excel
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Cortes Semanal', 'imprimir')">
                        <i class="fa-solid fa-print mr-2 jump-icon"></i> Imprimir
                    </button>
                </div>
            </div>
            <div class="mb-4 bg-zinc-100 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-2">Cortes mensual</h3>
                <div class="flex flex-wrap space-x-3">
                    <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Cortes Mensual', 'pdf')">
                        <i class="fa-solid fa-file-pdf mr-2 jump-icon"></i> Generar PDF
                    </button>
                    <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Cortes Mensual', 'excel')">
                        <i class="fa-solid fa-file-excel mr-2 jump-icon"></i> Generar Excel
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Cortes Mensual', 'imprimir')">
                        <i class="fa-solid fa-print mr-2 jump-icon"></i> Imprimir
                    </button>
                </div>
            </div>
            <div class="mb-4 bg-zinc-100 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-2">Cortes sin regreso</h3>
                <div class="flex flex-wrap space-x-3">
                    <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Cortes sin regreso', 'pdf')">
                        <i class="fa-solid fa-file-pdf mr-2 jump-icon"></i> Generar PDF
                    </button>
                    <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Cortes sin regreso', 'excel')">
                        <i class="fa-solid fa-file-excel mr-2 jump-icon"></i> Generar Excel
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Cortes sin regreso', 'imprimir')">
                        <i class="fa-solid fa-print mr-2 jump-icon"></i> Imprimir
                    </button>
                </div>
            </div>
            <div class="mb-4 bg-zinc-100 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-2">Retardos</h3>
                <div class="flex flex-wrap space-x-3">
                    <button class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Retardos', 'pdf')">
                        <i class="fa-solid fa-file-pdf mr-2 jump-icon"></i> Generar PDF
                    </button>
                    <button class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Retardos', 'excel')">
                        <i class="fa-solid fa-file-excel mr-2 jump-icon"></i> Generar Excel
                    </button>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded"
                        @click="generarArchivo('Retardos', 'imprimir')">
                        <i class="fa-solid fa-print mr-2 jump-icon"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </PrincipalLayout>
</template>

<style>
    .jump-icon:hover i {
        transition: transform 0.2s ease-in-out;
        transform: translateY(-3px);
    }
</style>