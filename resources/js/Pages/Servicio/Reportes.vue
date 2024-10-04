<script setup>
import ReportesEntrada from '../../Components/Reportes/ReportesEntrada.vue';
import ReportesCortes from '../../Components/Reportes/ReportesCortes.vue';
import ReportesCortesConRegreso from '../../Components/Reportes/ReportesCortesConRegreso.vue';
import ReportesCortesSinRegreso from '../../Components/Reportes/ReportesCortesSinRegreso.vue';
import ReportesEntradasTardes from '../../Components/Reportes/ReportesEntradasTardes.vue';
import ReportesDiasTrabajados from '../../Components/Reportes/ReportesDiasTrabajados.vue';
import ReportesCastigos from '../../Components/Reportes/ReportesCastigos.vue';
import ReportesUltimasCorridas from '../../Components/Reportes/ReportesUltimasCorridas.vue';
import ServicioLayout from '../../Layouts/ServicioLayout.vue';
import ReporteOperadoresSinTrabajar from '../../Components/Reportes/ReporteOperadoresSinTrabajar.vue';

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    usuario: { type: Object},
    unidad: {
        type: Object,
        default: () => ({}),
    },
    operador: {
        type: Object,
        default: () => ({}),
    },
    tipoUltimaCorrida: {
        type: Object,
        default: () => ({}),
    },
    entrada: {
        type: Object,
        default: () => ({}),
    },
    corte: {
        type: Object,
        default: () => ({}),
    },
    ultimaCorrida: {
        type: Object,
        default: () => ({}),
    },
    castigo: {
        type: Object,
        default: () => ({}),
    },
});

</script>

<template>
    <ServicioLayout title="Reportes" :usuario="props.usuario">
        <div class="mt-1 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0 mb-2">Reportes</h2>
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-2"></div>

            <ReportesEntrada :unidad="props.unidad" :operador="props.operador" :entrada="props.entrada"/>

            <ReportesEntradasTardes :unidad="props.unidad" :operador="props.operador" :entrada="props.entrada"/>

            <ReportesCortes :unidad="props.unidad" :operador="props.operador" :corte="props.corte"/>

            <ReportesCortesConRegreso :unidad="props.unidad" :operador="props.operador" :corte="props.corte"/>

            <ReportesCortesSinRegreso :unidad="props.unidad" :operador="props.operador" :corte="props.corte"/>

            <ReportesUltimasCorridas :unidad="props.unidad" :operador="props.operador" :tipoUltimaCorrida="props.tipoUltimaCorrida" :ultimaCorrida="props.ultimaCorrida"/>

            <ReportesCastigos :unidad="props.unidad" :operador="props.operador" :castigo="props.castigo"/>

            <ReportesDiasTrabajados :unidad="props.unidad" :operador="props.operador"/>

            <ReporteOperadoresSinTrabajar :unidad="props.unidad" :operador="props.operador"/>

        </div>
    </ServicioLayout>
</template>

<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>