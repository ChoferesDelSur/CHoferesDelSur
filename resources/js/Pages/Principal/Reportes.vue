<script setup>
import Mensaje from '../../Components/Mensaje.vue';
import PrincipalLayout from '../../Layouts/PrincipalLayout.vue';
import Swal from 'sweetalert2';
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
import { ref, reactive } from 'vue';
import ReportesEntrada from '../../Components/Reportes/ReportesEntrada.vue';

// Cargar fuentes personalizadas
pdfMake.vfs = pdfFonts.pdfMake.vfs;

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    unidad: {
        type: Object,
        default: () => ({}),
    },
    operador: {
        type: Object,
        default: () => ({}),
    },
});

</script>

<template>
    <PrincipalLayout title="Reportes">
        <div class="mt-1 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0 mb-2">Reportes</h2>
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-2"></div>

            <ReportesEntrada :unidad="props.unidad" :operador="props.operador"/>
            
        </div>
    </PrincipalLayout>
</template>

<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>