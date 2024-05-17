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
    domingo: props.formacionUnidades.domingo,
    unidadesSeleccionadas: {} // Inicializa el objeto de unidades seleccionadas
});

watch(() => props.formacionUnidades, async (newVal) => {
    form.idFormacionUnidades = newVal.idFormacionUnidades;
    form.unidad = newVal.unidad;
    form.domingo = newVal.domingo;
}, { deep: true }
);

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

const validateSelect = (selectedValue) => {
    if (!selectedValue || Object.values(selectedValue).every(value => value === false)) {
        return false; // Si no se ha seleccionado ninguna opción
    }
    return true; // Si se ha seleccionado al menos una opción
};

const unidadError = ref('');

const save = async () => {
/*     unidadError.value = validateSelect(form.unidad) ? '' : 'Seleccione al menos una unidad';
    if (
        unidadError.value
    ) {
        return;
    } */

    form.post(route('principal.trabDomingo'), {
        data: form.data, // Envía los datos del formulario al controlador
        onSuccess: () => {
            close()
           /*  unidadError.value = ''; */
        }
    })
}

const obtenerDomingoAnterior = () => {
    const today = new Date();
    const dayOfWeek = today.getDay();
    const prevSunday = new Date(today);
    prevSunday.setDate(today.getDate() - dayOfWeek);
    return prevSunday;
};

// Formatear la fecha del domingo pasado
const formatearDomingoAnterior = (date) => {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('es-ES', options);
};

// Obtener la fecha del domingo pasado y formatearla
const DomingoAnterior = formatearDomingoAnterior(obtenerDomingoAnterior());

// Calcular la fecha del próximo domingo
const obtenerProximoDomingo = () => {
    const hoy = new Date();
    const diaDeSemana = hoy.getDay();
    // Calcula cuántos días faltan para llegar al próximo domingo (si hoy es domingo, será 7)
    const diasParaDomingo = 7 - diaDeSemana;
    const proxDomingo = new Date(hoy);
    // Suma los días restantes para llegar al próximo domingo
    proxDomingo.setDate(hoy.getDate() + diasParaDomingo);
    return proxDomingo;
};

// Formatear la fecha del próximo domingo
const formatearProximoDomingo = (date) => {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('es-ES', options);
};

// Obtener la fecha del próximo domingo y formatearla
const proximoDomingo = formatearProximoDomingo(obtenerProximoDomingo());


</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="(op === '1' ? save() : update())">
                <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Seleccione las unidades que trabajarán el próximo:
                    <strong >{{ proximoDomingo }}</strong>.
                </p>
                <div class="border-b border-gray-900/10 pb-12">
                    <div class="sm:col-span-2 px-4">
                        <label class="block text-sm font-medium leading-6 text-gray-900">Unidad <span
                                class="text-red-500">*</span></label>
                        <div class="mt-2" style="max-height: 380px; overflow-y: auto;">
                            <!-- Utilizamos v-for para iterar sobre cada opción de unidad -->
                            <div v-for="carro in unidad" :key="carro.idUnidad">
                                <!-- Cada opción es representada por un checkbox con su propio modelo -->
                                <input type="checkbox" :id="'unidad' + carro.idUnidad" :value="carro.idUnidad"
                                    v-model="form.unidadesSeleccionadas[carro.idUnidad]"
                                    class="rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <!-- Etiqueta para el checkbox -->
                                <label :for="'unidad' + carro.idUnidad" class="ml-2">{{ carro.numeroUnidad }}</label>
                            </div>
                        </div>
                    </div>
                    <!-- <div v-if="unidadError != ''" class="text-red-500 text-xs mt-1">{{ unidadError }}</div> -->
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" :id="'cerrar' + op"
                        class="text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-white py-2 px-4 rounded"
                        data-bs.dismiss="modal" @click="close">Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        :disabled="form.processing"> <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </Modal>
</template>