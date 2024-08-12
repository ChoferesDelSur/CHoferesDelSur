<script setup>
import { useForm, put } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import Modal from '../Modal.vue';
import { route } from '../../../../vendor/tightenco/ziggy/src/js';
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
    corte: {
        type: Object,
        default: () => ({}),
    },
    unidad: {
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
    idCorte: props.corte.idCorte,
    unidad: props.corte.idUnidad,
    horaCorte: props.corte.horaCorte,
    causa: props.corte.corte,
    horaRegreso: props.corte.horaRegreso,
});

watch(() => props.corte, async (newVal) => {
    form.idCorte = newVal.idCorte;
    form.unidad = newVal.unidad;
    form.horaCorte = newVal.horaCorte;
    form.causa = newVal.causa;
    form.horaRegreso = newVal.horaRegreso;
}, { deep: true }
);

// Validación de los select 
const validateSelect = (selectedValue) => {
    if (selectedValue == undefined) {
        return false;
    }
    return true;
};

// Validación de cadenas no vacias
const validateStringNotEmpty = (value) => {
    return typeof value === 'string' && value.trim() !== '';
}

const unidadError = ref('');
const horaCorteError = ref('');
const causaError = ref('');
const horaRegresoError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

const save = async () => {
    horaCorteError.value = validateSelect(form.horaCorte) ? '' : 'Seleccione la hora de corte';
    unidadError.value = validateSelect(form.unidad) ? '' : 'Seleccione una unidad';
    causaError.value = validateStringNotEmpty(form.causa) ? '' : 'Ingrese la causa del corte';


    if (
        horaCorteError.value || unidadError.value || causaError.value
    ) {
        return;
    }
    form.post(route('principal.registarCorte'), {
        onSuccess: () => {
            close()
            horaCorteError.value = '';
            unidadError.value = '';
            causaError.value = '';
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
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Rellene el formulario para poder registrar la
                        hora de corte de una unidad. Los campos con <span class="text-red-500">*</span> son
                        obligatorios.
                    </p>
                    <div class="flex flex-wrap -mx-4">
                        <div class="sm:col-span-2 px-4">
                            <label for="unidad" class="block text-sm font-medium leading-6 text-gray-900">Unidad <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="unidad" :id="'unidad' + op" v-model="form.unidad"
                                    placeholder="Seleccione la unidad"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione la unidad</option>
                                    <option v-for="carro in unidadesConOperador" :key="carro.idUnidad" :value="carro.idUnidad">
                                        {{ carro.numeroUnidad }}

                                    </option>
                                </select>
                            </div>
                            <div v-if="unidadError != ''" class="text-red-500 text-xs mt-1">{{ unidadError }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="horaCorte" class="block text-sm font-medium leading-6 text-gray-900">Hora de
                                corte <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="time" name="horaCorte" :id="'horaCorte' + op" v-model="form.horaCorte"
                                    placeholder="Seleccione la hora de corte"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                            </div>
                            <div v-if="horaCorteError != ''" class="text-red-500 text-xs">{{ horaCorteError }}</div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="causa" class="block text-sm font-medium leading-6 text-gray-900">Causa <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2"><!-- Espacio entre titulo y cuadro de texto -->
                                <input type="text" name="causa" :id="'causa' + op" v-model="form.causa"
                                    placeholder="Ingrese la causa del corte"
                                    class="block w-64 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                            <!--  // Div para mostrar las validaciones en dado caso que no sean correctas -->
                            <div v-if="causaError != ''" class="text-red-500 text-xs mt-1">{{ causaError }}</div>
                            <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="horaRegreso" class="block text-sm font-medium leading-6 text-gray-900">Hora de
                                regreso</label>
                            <div class="mt-2">
                                <input type="time" name="horaRegreso" :id="'horaRegreso' + op"
                                    v-model="form.horaRegreso" placeholder="Seleccione la hora de regreso"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
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
</style>