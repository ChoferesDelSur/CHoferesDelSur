<script setup>
import ReporteOperadoresSinTrabajar from '../../Components/Reportes/ReporteOperadoresSinTrabajar.vue';
import ReportesDiasTrabajados from '../../Components/Reportes/ReportesDiasTrabajados.vue';
import ReportesMovimientos from '../../Components/Reportes/ReportesMovimientos.vue';
import RHLayout from '../../Layouts/RHLayout.vue';

const props = defineProps({
    message: { String, default: '' },
    color: { String, default: '' },
    usuario: { type: Object },
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
    movimiento: {
        type: Object,
        default: () => ({}),
    },
    directivo: {
        type: Object,
        default: () => ({}),
    },
    tipoMovimiento: {
        type: Object,
        default: () => ({}),
    },
});

</script>

<template>
    <RHLayout title="Reportes" :usuario="props.usuario">
        <div class="mt-1 bg-white p-4 shadow rounded-lg h-5/6">
            <h2 class="font-bold text-center text-xl pt-0 mb-2">Reportes</h2>
            <div class="bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-2"></div>

            <!-- <ReportesDiasTrabajados :unidad="props.unidad" :operador="props.operador" /> -->

            <ReportesMovimientos :unidad="props.unidad" :operador="props.operador" :directivo="props.directivo"
                :movimiento="props.movimiento" :tipoMovimiento="props.tipoMovimiento" />

            <ReporteOperadoresSinTrabajar :unidad="props.unidad" :operador="props.operador">
            </ReporteOperadoresSinTrabajar>

        </div>
    </RHLayout>
</template>

<style>
.jump-icon:hover i {
    transition: transform 0.2s ease-in-out;
    transform: translateY(-3px);
}
</style>