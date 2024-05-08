<script setup>
import { useForm, put } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import Modal from '../Modal.vue';
import { route } from '../../../../vendor/tightenco/ziggy/src/js';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
        hora: String,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    formacionUnidades: {
        type: Object,
        default: () => ({}),
    },
    unidad: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const emit = defineEmits(['close']);

const form = useForm({
    idFormacionUnidades: props.formacionUnidades.idFormacionUnidades,
    unidad: props.formacionUnidades.idUnidad,
    horaEntrada: props.formacionUnidades.horaEntrada,
    extremo: props.formacionUnidades.extremo,
});

watch(() => props.formacionUnidades, async (newVal) => {
    form.idFormacionUnidades = newVal.idFormacionUnidades;
    form.unidad = newVal.unidad;
    form.horaEntrada = newVal.horaEntrada;
    form.extremo = newVal.extremo;
}, { deep: true }
);

</script>
<template>

</template>