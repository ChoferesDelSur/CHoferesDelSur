<script setup>
import { useForm, put } from '@inertiajs/inertia-vue3';
import { ref, watch, computed } from 'vue';
import Modal from '../Modal.vue';
//import { route } from '../../../../vendor/tightenco/ziggy/src/js';
import { route } from 'ziggy-js';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import 'vue-select';
import axios from 'axios';

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
    entrada: {
        type: Object,
        default: () => ({}),
    },
    unidad: {
        type: Object,
        default: () => ({}),
    },
    operador: {
        type: Object,
        default: () => ({}),
    },
    ruta: {
        type: Object,
        default: () => ({}),
    },
    unidadesConOperador: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})
const emit = defineEmits(['close']);

const form = useForm({
    idEntrada: props.entrada.idEntrada,
    unidad: props.entrada.idUnidad,
    horaEntrada: props.entrada.horaEntrada,
    extremo: props.entrada.extremo,
});

// Computed property para obtener el nombre de la ruta
const rutaNombre = computed(() => {
    const unidadSeleccionada = props.unidadesConOperador.find(unidad => unidad.idUnidad === form.unidad);
    return unidadSeleccionada && unidadSeleccionada.ruta ? unidadSeleccionada.ruta.nombreRuta : 'Nombre de ruta'; // Obtén solo el nombre de la ruta
});

const operadorNombre = computed(() => {
    const unidadSeleccionada = props.unidadesConOperador.find(unidad => unidad.idUnidad === form.unidad);
    return unidadSeleccionada && unidadSeleccionada.operador ? unidadSeleccionada.operador.nombre_completo : 'Nombre de operador';
});

watch(() => props.entrada, async (newVal) => {
    form.idEntrada = newVal.idEntrada;
    form.unidad = newVal.unidad;
    form.horaEntrada = newVal.horaEntrada;
    form.extremo = newVal.extremo;
}, { deep: true }
);

// Validación de los select 
const validateSelect = (selectedValue) => {
    if (selectedValue == undefined) {
        return false;
    }
    return true;
};

const validateRadio = (selectedValue) => {
    return selectedValue !== null && selectedValue !== undefined; // Validar si el valor seleccionado no es 'null' ni 'undefined'
};

const unidadError = ref('');
const horaEntradaError = ref('');
const extremoError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

const save = async () => {
    horaEntradaError.value = validateSelect(form.horaEntrada) ? '' : 'Seleccione la hora de entrada';
    unidadError.value = validateSelect(form.unidad) ? '' : 'Seleccione una unidad';
    extremoError.value = validateRadio(form.extremo) ? '' : 'Seleccione una opción';

    if (
        horaEntradaError.value || unidadError.value || extremoError.value
    ) {
        return;
    }
    form.post(route('servicio.registarHoraEntrada'), {
        onSuccess: () => {
            close()
            horaEntradaError.value = '';
            unidadError.value = '';
            extremoError.value = '';
        }
    })
}
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="(op === '1' ? save() : update())">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Rellene todos los campos para poder registrar
                        la
                        hora de Entrada de una unidad
                    </p>
                    <div class="flex flex-wrap -mx-4">
                        <div class="md:col-span-2">
                            <div class="sm:col-span-2" hidden> <!-- Definir el tamaño del cuadro de texto -->
                                <label for="idOperador"
                                    class="block text-sm font-medium leading-6 text-gray-900">idEntrada</label>
                                <div class="mt-1">
                                    <input type="number" name="idEntrada" v-model="form.idEntrada"
                                        placeholder="Ingrese id de hora de entrada" :id="'idEntrada' + op"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2 w-56 px-4">
                            <label for="unidad" class="block text-sm font-medium leading-6 text-gray-900">Unidad</label>
                            <div class="mt-2">
                                <v-select v-model="form.unidad"
                                    :options="unidadesConOperador.map(carro => ({ label: carro.numeroUnidad, value: carro.idUnidad }))"
                                    placeholder="Seleccione la unidad" :reduce="unidad => unidad.value" class="w-full">
                                </v-select>
                            </div>
                            <div v-if="unidadError != ''" class="text-red-500 text-xs mt-1">{{ unidadError }}</div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="horaEntrada" class="block text-sm font-medium leading-6 text-gray-900">Hora de
                                entrada</label>
                            <div class="mt-2">
                                <input type="time" name="horaEntrada" :id="'horaEntrada' + op"
                                    v-model="form.horaEntrada" placeholder="Seleccione la hora de entrada"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                            </div>
                            <div v-if="horaEntradaError != ''" class="text-red-500 text-xs">{{ horaEntradaError }}</div>
                        </ div>
                        <div class="sm:col-span-2 px-4">
                            <label class="block text-sm font-medium leading-6 text-gray-900">¿Es de extremo?</label>
                            <div class="mt-2">
                                <input type="radio" id="si" value="SI" v-model="form.extremo">
                                <label for="si" class="radio-label"> Sí</label>
                                <input type="radio" id="no" value="NO" v-model="form.extremo">
                                <label for="no" class="radio-label"> No</label>
                            </div>
                            <div v-if="extremoError != ''" class="text-red-500 text-xs">{{ extremoError }}</div>
                        </div>
                        <!-- Operador -->
                        <div class="sm:col-span-2 px-4">
                            <label class="block text-sm font-medium leading-6 text-gray-900">Operador</label>
                            <div class="mt-2">
                                <span
                                    class="block w-72 rounded-md border border-gray-300 px-1.5 py-1.5 text-gray-900 bg-gray-100 shadow-sm sm:text-sm sm:leading-6">{{
                                    operadorNombre }}</span>
                            </div>
                        </div>

                        <div class="sm:col-span-2 px-4">
                            <label for="ruta" class="block text-sm font-medium leading-6 text-gray-900">Ruta</label>
                            <div class="mt-2">
                                <span
                                    class="block w-72 rounded-md border border-gray-300 px-1.5 py-1.5 text-gray-900 bg-gray-100 shadow-sm sm:text-sm sm:leading-6">
                                    {{ rutaNombre }} <!-- Solo se mostrará el nombre de la ruta -->
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" :id="'cerrar' + op"
                        class="text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-white py-2 px-4 rounded"
                        data-bs.dismiss="modal" @click="close"><i class="fa-solid fa-ban"></i> Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        :disabled="form.processing"> <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </Modal>
</template>
<style>
@import "vue-select/dist/vue-select.css";

.radio-label {
    margin-right: 10px;
    /* Ajusta este valor según lo que necesites */
}

.v-select .vs__dropdown-menu {
    z-index: 9999;
    /* Asegura que el menú desplegable se muestre sobre otros elementos */
}

.modal-content {
    overflow: visible !important;
    /* Permite que el menú desplegable se extienda más allá del modal */
}
</style>