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
    ruta: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const emit = defineEmits(['close']);

const form = useForm({
    idRuta: props.ruta.idRuta,
    nombreRuta: props.ruta.nombreRuta,
});

watch(() => props.ruta, async (newVal) => {
    form.idRuta = newVal.idRuta;
    form.nombreRuta = newVal.nombreRuta;
}, { deep: true }
);

// Variables para los mensajes de validación
const nombreRutaError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

// Validación de cadenas no vacias
const validateStringNotEmpty = (value) => {
    return typeof value === 'string' && value.trim() !== '';
}

const save = async () => {
    nombreRutaError.value = validateStringNotEmpty(form.nombreRuta) ? '' : 'Ingrese el nombre de la ruta';

    if (
        nombreRutaError.value
    ) {
        return;
    }
    form.post(route('servicio.addRuta'), {
        onSuccess: () => {
            close()
            nombreRutaError.value = '';
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
                    <p class="mt-1 text-sm leading-6 text-gray-600">Rellene el campo para registrar una nueva ruta
                    </p>
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-1" hidden> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="idRuta" class="block text-sm font-medium leading-6 text-gray-900">idRuta</label>
                            <div class="mt-2">
                                <input type="number" name="idRuta" v-model="form.idRuta"
                                    placeholder="Ingrese id de la ruta" :id="'idRuta' + op"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>
                    <div class="sm:col-span-2"> <!-- Definir el tamaño del cuadro de texto -->
                        <label for="nombreRuta" class="block text-sm font-medium leading-6 text-gray-900">Nombre de la
                            ruta</label>
                        <div class="mt-2"><!-- Espacio entre titulo y cuadro de texto -->
                            <input type="text" name="nombreRuta" :id="'nombreRuta' + op" v-model="form.nombreRuta"
                                placeholder="Ingrese el nombre de la ruta"
                                class="block w-full sm:w-1/2 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                        <!--  // Div para mostrar las validaciones en dado caso que no sean correctas -->
                        <div v-if="nombreRutaError != ''" class="text-red-500 text-xs mt-1">{{ nombreRutaError }}</div>
                        <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
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