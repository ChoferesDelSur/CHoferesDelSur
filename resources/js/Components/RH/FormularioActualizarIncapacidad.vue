<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, watch} from 'vue';
import Modal from '../Modal.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    operador: {
        type: Object,
        default: () => ({}),
    },
    operadoresAlta: {
        type: Object,
        default: () => ({}),
    },
    incapacidad: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const emit = defineEmits(['close']);

// Inicialización de `incapacidad`
const incapacidad = ref({
    motivo: '',
    numeroDias: '',
    fechaInicio: '',
    fechaFin: '',
});

const form = useForm({
    _method: 'PUT', // Método simulado para Laravel
    idIncapacidad: props.incapacidad.idIncapacidad,
    motivo: props.incapacidad.motivo,
    numeroDias: props.incapacidad.numeroDias,
    fechaInicio: props.incapacidad.fechaInicio,
    fechaFin: props.incapacidad.fechaFin,
    operador: props.incapacidad.idOperador,
});

watch(() => props.incapacidad, async (newVal) => {
    form.idIncapacidad = newVal.idIncapacidad;
    form.motivo = newVal.motivo;
    form.numeroDias = newVal.numeroDias;
    form.fechaInicio = newVal.fechaInicio;
    form.fechaFin = newVal.fechaFin;
    form.operador = newVal.idOperador;
}, { deep: true }
);

// Watcher para numeroDias y fechaInicio
watch([() => form.numeroDias, () => form.fechaInicio], ([newNumeroDias, newFechaInicio]) => {
    if (newNumeroDias && newFechaInicio) {
        const startDate = new Date(newFechaInicio);
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + parseInt(newNumeroDias, 10));
        form.fechaFin = endDate.toISOString().split('T')[0]; // Formato YYYY-MM-DD
    }
});

// Variables para los mensajes de validación
const motivoError = ref('');
const numDiasError = ref('');
const fechaInicioError = ref('');
const fechaFinError = ref('');
const operadorError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

// Validación de cadenas no vacias
const validateStringNotEmpty = (value) => {
    return typeof value === 'string' && value.trim() !== '';
}

// Validación de cadenas no vacías y solo números enteros
const validateIntegerField = (value) => {
    // Verificar que el valor no sea null o undefined
    if (value == null) {
        return false;
    }

    // Verificar que el valor sea un número entero
    return Number.isInteger(value) && value >= 0;
}
const validateDateField = (dateValue) => {
    // Verificar que el campo no sea null o undefined
    if (dateValue === null || dateValue === undefined) {
        return false;
    }
    // Convertir cadenas de texto a Date si es necesario
    if (typeof dateValue === 'string' || dateValue instanceof String) {
        dateValue = new Date(dateValue);
    }
    // Verificar que el valor sea una instancia de Date
    if (!(dateValue instanceof Date)) {
        return false;
    }
    // Verificar que la fecha sea válida
    if (isNaN(dateValue.getTime())) {
        return false;
    }
    return true;
};

const update = async () => {
    // Validación de los campos del formulario principal
    motivoError.value = validateStringNotEmpty(form.motivo) ? '' : 'Ingrese el motivo de la incapacidad';
    numDiasError.value = validateIntegerField(form.numeroDias) ? '' : 'Ingrese el número de días de incapacidad';
    fechaInicioError.value = validateDateField(form.fechaInicio) ? '' : 'Ingrese la fecha de inicio de incapacidad';
    fechaFinError.value = validateDateField(form.fechaFin) ? '' : 'Ingrese la fecha de fin de incapacidad';
    // Verificar si hay algún error antes de enviar el formulario
    if (
        motivoError.value || numDiasError.value || fechaInicioError.value || fechaFinError.value
    ) {
        return;
    }

    // Si no hay errores, enviar el formulario
    form.post(route('rh.actualizarIncapacidad', form.idIncapacidad), {
        onSuccess: () => {
            close();
            // Limpia los errores y campos si es necesario

            motivoError.value = '';
            numDiasError.value = '';
            fechaInicioError.value = '';
            fechaFinError.value = '';
            // Limpia los campos de incapacidad
        }
    });
};

const getOperadorNombre = (idOperador) => {
    const operador = props.operador.find(op => op.idOperador === idOperador);
    return operador ? operador.nombre_completo : 'Operador no encontrado';
};

</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="update">
                <div class="border-b border-gray-900/10 pb-12">
                    <h1 class="text-2xl font-semibold leading-7 text-gray-900">{{ title }}</h1>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Rellene el formulario para poder registrar una
                        incapacidad. Los campos con <span class="text-red-500">*</span> son obligatorios.
                    </p>
                    <!-- Campos adicionales para Incapacidad -->
                    <div class="flex flex-wrap -mx-4">
                        <div class="md:col-span-2">
                            <div class="sm:col-span-2" hidden>
                                <!-- Definir el tamaño del cuadro de texto -->
                                <label for="idIncapacidad"
                                    class="block text-sm font-medium leading-6 text-gray-900">idIncapacidad</label>
                                <div class="mt-1">
                                    <input type="number" name="idIncapacidad" v-model="form.idIncapacidad"
                                        placeholder="Ingrese id de la incapacidad" :id="'idIncapacidad' + op"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-2">
                            <label for="motivo" class="block text-sm font-medium leading-6 text-gray-900">Motivo
                                <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="motivo" v-model="form.motivo"
                                    placeholder="Ingrese el motivo de la incapacidad"
                                    class="block w-64 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="motivoError != ''" class="text-red-500 text-xs mt-1">{{
                                motivoError
                                }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-2">
                            <label for="numeroDias" class="block text-sm font-medium leading-6 text-gray-900">Número
                                de Días <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="number" name="numeroDias" v-model="form.numeroDias"
                                    placeholder="Ingrese el número de días"
                                    class="block w-32 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="numDiasError != ''" class="text-red-500 text-xs mt-1">{{
                                numDiasError
                                }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-2">
                            <label for="fechaInicio" class="block text-sm font-medium leading-6 text-gray-900">Fecha
                                de Inicio <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="date" name="fechaInicio" v-model="form.fechaInicio"
                                    placeholder="Ingrese la fecha de inicio"
                                    class="block w-48 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="fechaInicioError != ''" class="text-red-500 text-xs mt-1">{{
                                fechaInicioError }}</div>
                        </div>
                        <div class="sm:col-span-2 px-2">
                            <label for="fechaFin" class="block text-sm font-medium leading-6 text-gray-900">Fecha de
                                Fin <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="date" name="fechaFin" v-model="form.fechaFin"
                                    placeholder="Ingrese la fecha de fin"
                                    class="block w-48 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="fechaFinError != ''" class="text-red-500 text-xs mt-1">{{
                                fechaFinError
                                }}</div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="operador"
                                class="block text-sm font-medium leading-6 text-gray-900">Operador</label>
                            <div class="mt-2">
                                <p
                                    class="block rounded-md py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6">
                                    {{ getOperadorNombre(form.operador) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" :id="'cerrar' + op"
                        class="text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-white py-2 px-2 rounded"
                        data-bs.dismiss="modal" @click="close"><i class="fa-solid fa-ban"></i> Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-2 rounded"
                        :disabled="form.processing"> <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </Modal>
</template>
<style>
@import "vue-select/dist/vue-select.css";

hr {
    border: 1px solid #e5e7eb;
    /* Ajuste del color de la línea divisoria */
}
</style>