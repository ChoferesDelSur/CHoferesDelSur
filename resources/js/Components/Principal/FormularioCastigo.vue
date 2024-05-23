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
    castigo: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const emit = defineEmits(['close']);

const form = useForm({
    idCastigo: props.castigo.idCastigo,
    castigo: props.castigo.castigo,
    observaciones: props.castigo.observaciones,
    horaInicio: props.castigo.horaInicio,
    horaFin: props.castigo.horaFin,
    unidad: props.castigo.idUnidad,
});

watch(() => props.castigo, async (newVal) => {
    form.idCastigo = newVal.idCastigo;
    form.castigo = newVal.castigo;
    form.observaciones = newVal.observaciones;
    form.horaInicio = newVal.horaInicio;
    form.horaFin = props.horaFin;
    form.unidad = props.unidad;
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
const horaInicioError = ref('');
const castigoError = ref('');
const horaFinError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

const save = async () => {
    console.log("Entró en save de FormularioCastigo");
    horaInicioError.value = validateSelect(form.horaInicio) ? '' : 'Seleccione la hora de inicio del castigo';
    console.log("Hora inicio: "+ form.horaInicio);
    unidadError.value = validateSelect(form.unidad) ? '' : 'Seleccione una unidad';
    console.log("Unidad: "+ form.unidad);
    castigoError.value = validateStringNotEmpty(form.castigo) ? '' : 'Ingrese motivo del castigo';
    console.log("Castigo: "+form.castigo);
    horaFinError.value = validateSelect(form.horaFin) ? '' : 'Seleccione la hora de fin del castigo';
    console.log("Hora fin: "+ form.horaFin);


    if (
        horaInicioError.value || unidadError.value || castigoError.value || horaFinError.value
    ) {
        return;
    }
    form.post(route('principal.registrarCastigo'), {
        onSuccess: () => {
            console.log("Estoy después de principal.registrarCastigo");
            close()
            horaInicioError.value = '';
            unidadError.value = '';
            castigoError.value = '';
            horaFinError.value = '';
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
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Rellene el formulario para poder registrar el castigo. Los campos con <span class="text-red-500">*</span> son
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
                                    <option v-for="carro in unidad" :key="carro.idUnidad" :value="carro.idUnidad">
                                        {{ carro.numeroUnidad }}

                                    </option>
                                </select>
                            </div>
                            <div v-if="unidadError != ''" class="text-red-500 text-xs mt-1">{{ unidadError }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="castigo" class="block text-sm font-medium leading-6 text-gray-900">Castigo <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2"><!-- Espacio entre titulo y cuadro de texto -->
                                <input type="text" name="castigo" :id="'castigo' + op" v-model="form.castigo"
                                    placeholder="Ingrese el motivo del castigo"
                                    class="block w-64 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                            <!--  // Div para mostrar las validaciones en dado caso que no sean correctas -->
                            <div v-if="castigoError != ''" class="text-red-500 text-xs mt-1">{{ castigoError }}</div>
                            <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="horaInicio" class="block text-sm font-medium leading-6 text-gray-900">Hora de inicio <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="time" name="horaInicio" :id="'horaInicio' + op" v-model="form.horaInicio"
                                    placeholder="Seleccione la hora de inicio"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                            </div>
                            <div v-if="horaInicioError != ''" class="text-red-500 text-xs">{{ horaInicioError }}</div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="horaFin" class="block text-sm font-medium leading-6 text-gray-900">Hora fin <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="time" name="horaFin" :id="'horaFin' + op" v-model="form.horaFin"
                                    placeholder="Seleccione la horade finalización"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                            </div>
                            <div v-if="horaFinError != ''" class="text-red-500 text-xs">{{ horaFinError }}</div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="observaciones" class="block text-sm font-medium leading-6 text-gray-900">Observaciones</label>
                            <div class="mt-2"><!-- Espacio entre titulo y cuadro de texto -->
                                <input type="text" name="observaciones" :id="'observaciones' + op" v-model="form.observaciones"
                                    placeholder="Ingrese sus observaciones"
                                    class="block w-64 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
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