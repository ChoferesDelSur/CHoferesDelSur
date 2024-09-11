<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, computed, watch } from 'vue';
import Modal from '../Modal.vue';

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
    personal: {
        type: Object,
        default: () => ({}),
    },
    vacaciones: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})
const emit = defineEmits(['close']);

const form = useForm({
    idVacaciones: props.vacaciones.idVacaciones,
    motivo: props.vacaciones.motivo,
    numeroDias: props.vacaciones.numeroDias,
    fechaInicio: props.vacaciones.fechaInicio,
    fechaFin: props.vacaciones.fechaFin,
    persona: props.vacaciones.idPersonal
});

// Variables para los mensajes de validación
const motivoError = ref('');
const numeroDiasError = ref('');
const fechaInicioError = ref('');
const fechaFinError = ref('');
const personaError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

// Calcular la fecha de fin automáticamente
watch([() => form.numeroDias, () => form.fechaInicio], ([newNumeroDias, newFechaInicio]) => {
    if (newNumeroDias > 0 && newFechaInicio) {
        // Convertir la fecha de inicio a un objeto Date
        const fechaInicioDate = new Date(newFechaInicio);

        // Sumar los días especificados a la fecha de inicio
        fechaInicioDate.setDate(fechaInicioDate.getDate() + parseInt(newNumeroDias));

        // Formatear la fecha de fin en formato 'YYYY-MM-DD'
        const year = fechaInicioDate.getFullYear();
        const month = String(fechaInicioDate.getMonth() + 1).padStart(2, '0'); // Los meses en JavaScript son 0-indexados
        const day = String(fechaInicioDate.getDate()).padStart(2, '0');

        form.fechaFin = `${year}-${month}-${day}`;
    }
}, { immediate: true });

// Validación de los select 
const validateSelect = (selectedValue) => {
    if (selectedValue == undefined) {
        return false;
    }
    return true;
};

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

// Validación de cadenas no vacias
const validateStringNotEmpty = (value) => {
    return typeof value === 'string' && value.trim() !== '';
}

const validateIntegerField = (value) => {
    // Verificar que el valor no sea null o undefined
    if (value == null) {
        return false;
    }

    // Verificar que el valor sea un número entero
    return Number.isInteger(value) && value >= 0;
}

const save = async () => {
    personaError.value = validateSelect(form.persona) ? '' : 'Seleccione al personal';
    motivoError.value = validateStringNotEmpty(form.motivo) ? '' : 'Ingrese el motivo';
    numeroDiasError.value = validateIntegerField(form.numeroDias) ? '' : 'Número de días no válido';
    fechaInicioError.value = validateDateField(form.fechaInicio) ? '' : 'Fecha de inicio no válido';
    fechaFinError.value = validateDateField(form.fechaFin) ? '' : 'Fecha de fin no válido';

    if (
        personaError.value || motivoError.value || numeroDiasError.value || fechaInicioError.value || fechaFinError.value
    ) {
        return;
    }
    form.post(route('rh.addVacaciones'), {
        onSuccess: () => {
            close()
            personaError.value = '';
            motivoError.value = '';
            numeroDiasError.value = '';
            fechaInicioError.value = '';
            fechaFinError.value = '';
        }
    })
}

</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="save">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Rellene el formulario para poder registrar las
                        vacaciones. Los campos con <span class="text-red-500">*</span> son obligatorios.
                    </p>
                    <div class="flex flex-wrap -mx-4">
                        <div class="sm:col-span-2">
                            <div class="sm:col-span-1" hidden> <!-- Definir el tamaño del cuadro de texto -->
                                <label for="idVacaciones"
                                    class="block text-sm font-medium leading-6 text-gray-900">idVacaciones</label>
                                <div class="mt-2">
                                    <input type="number" name="idVacaciones" v-model="form.idVacaciones"
                                        placeholder="Ingrese id de vacaciones" :id="'idVacaciones' + op"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="personal" class="block text-sm font-medium leading-6 text-gray-900">Personal
                                <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="personal" :id="'personal' + op" v-model="form.persona"
                                    placeholder="Seleccione al personal"
                                    class="block w-54 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione al personal</option>
                                    <option v-for="persona in personal" :key="persona.idPersonal"
                                        :value="persona.idPersonal">
                                        {{ persona.nombre_completo }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="personaError != ''" class="text-red-500 text-xs mt-1">{{ personaError }}</div>
                        </div>
                        <div class="md:col-span-2 px-2"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="motivo" class="block text-sm font-medium leading-6 text-gray-900">Motivo <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2"><!-- Espacio entre titulo y cuadro de texto -->
                                <input type="text" name="motivo" :id="'motivo' + op" v-model="form.motivo"
                                    placeholder="Ingrese el motivo"
                                    class="block w-72 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="motivoError != ''" class="text-red-500 text-xs mt-1">{{ motivoError }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-2">
                            <label for="numDias" class="block text-sm font-medium leading-6 text-gray-900">Número de
                                Días <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="number" name="numDias" :id="'numDias' + op" v-model="form.numeroDias"
                                    placeholder=" "
                                    class="block w-28 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="numeroDiasError != ''" class="text-red-500 text-xs mt-1">{{ numeroDiasError }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="fechaInicio" class="block text-sm font-medium leading-6 text-gray-900">Fecha
                                Inicio <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="date" name="fechaInicio" :id="'fechaInicio' + op"
                                    v-model="form.fechaInicio" placeholder="Ingrese la fecha de inicio"
                                    class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="fechaInicioError != ''" class="text-red-500 text-xs mt-1">{{
                                fechaInicioError }}</div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="fechaFin" class="block text-sm font-medium leading-6 text-gray-900">Fecha
                                Fin <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="date" name="fechaFin" :id="'fechaFin' + op"
                                    v-model="form.fechaFin" placeholder="Ingrese la fecha de fin"
                                    class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="fechaFinError != ''" class="text-red-500 text-xs mt-1">{{
                                fechaFinError}}</div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" :id="'cerrar' + op"
                        class="text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-white py-2 px-4 rounded"
                        data-bs.dismiss="modal" @click="close"><i class="fa-solid fa-ban"></i> Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-500 text-white font-semibold py-2 px-4 rounded"
                        :disabled="form.processing"> <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </Modal>
</template>