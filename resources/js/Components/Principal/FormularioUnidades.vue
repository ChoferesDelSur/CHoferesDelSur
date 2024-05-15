<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import Modal from '../Modal.vue';
//import Modal from '../Modal.vue';

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
    unidad: {
        type: Object,
        default: () => ({}),
    },
    operadoresDisp: {
        type: Object,
        default: () => ({}),
    },
    ruta: {
        type: Object,
        default: () => ({}),
    },
    directivo: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const emit = defineEmits(['close']);

const form = useForm({
    idUnidad: props.unidad.idUnidad,
    numeroUnidad: props.unidad.numeroUnidad,
    ruta: props.unidad.idRuta,
    operador: props.unidad.idOperador,
    directivo: props.directivo.idDirectivo,
});

watch(() => props.unidad, async (newVal) => {
    form.idUnidad = newVal.idUnidad;
    form.numeroUnidad = newVal.numeroUnidad;
    form.ruta = newVal.idRuta;
    form.operador = newVal.idOperador;
    form.directivo = newVal.idDirectivo;
}, { deep: true }
);

// Variables para los mensajes de validación
const numeroUnidadError = ref('');
const rutaError = ref('');
const operadorError = ref('');
const directivoError = ref('');

//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}

// Validación de cadenas no vacias
const validateStringNotEmpty = (value) => {
    return typeof value === 'string' && value.trim() !== '';
}

// Validación de los select 
const validateSelect = (selectedValue) => {
    if (selectedValue == undefined) {
        return false;
    }
    return true;
};

const save = async () => {
    numeroUnidadError.value = validateStringNotEmpty(form.numeroUnidad) ? '' : 'Ingrese el número de la unidad';
    rutaError.value = validateSelect(form.ruta) ? '' : 'Selecciones la ruta';
    /* operadorError.value = validateSelect(form.operador) ? '' : 'Seleccione el operador'; */
    directivoError.value = validateSelect(form.directivo) ? '' : 'Seleccione el dueño de la unidad';

    if (
        numeroUnidadError.value || rutaError.value || /* operadorError.value || */ directivoError.value
    ) {
        return;
    }
    form.post(route('principal.addUnidad'), {
        onSuccess: () => {
            close()
            numeroUnidadError.value = '';
            rutaError.value = '';
            /* operadorError.value = ''; */
            directivoError.value = '';
        }
    })
}

const update = async () => {
    numeroUnidadError.value = validateStringNotEmpty(form.numeroUnidad) ? '' : 'Ingrese el número de la unidad';
    rutaError.value = validateSelect(form.ruta) ? '' : 'Selecciones la ruta';
    /* operadorError.value = validateSelect(form.operador) ? '' : 'Seleccione el operador'; */
    directivoError.value = validateSelect(form.directivo) ? '' : 'Seleccione el dueño de la unidad';

    if (
        numeroUnidadError.value || rutaError.value /* || operadorError.value */ || directivoError.value
    ) {
        return;
    }

    var idUnidad = document.getElementById('idUnidad2').value;
    console.log("idUnidad:" + idUnidad);
    form.put(route('principal.actualizarUnidad', idUnidad), {
        onSuccess: () => {
            close()
            console.log("idUnidad Editando:" + idUnidad);
            numeroUnidadError = '';
            rutaError = '';
            /* operadorError = ''; */
            directivoError = '';
        }
    });
}
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="(op === '1' ? save() : update())">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Rellene el formulario para poder registrar una
                        unidad. Los campos con <span class="text-red-500">*</span> son obligatorios
                    </p>
                    <div class="flex flex-wrap -mx-4">
                        <div class="sm:col-span-2">
                            <div class="sm:col-span-1" hidden> <!-- Definir el tamaño del cuadro de texto -->
                                <label for="idUnidad"
                                    class="block text-sm font-medium leading-6 text-gray-900">idUnidad</label>
                                <div class="mt-2">
                                    <input type="number" name="idUnidad" v-model="form.idUnidad"
                                        placeholder="Ingrese id de la unidad" :id="'idUnidad' + op"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="numeroUnidad" class="block text-sm font-medium leading-6 text-gray-900">Número
                                de unidad <span class="text-red-500">*</span></label>
                            <div class="mt-2"><!-- Espacio entre titulo y cuadro de texto -->
                                <input type="text" name="numeroUnidad" :id="'numeroUnidad' + op"
                                    v-model="form.numeroUnidad" placeholder="Ingrese el número de la unidad"
                                    class="block w-60 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                            <!--  // Div para mostrar las validaciones en dado caso que no sean correctas -->
                            <div v-if="numeroUnidadError != ''" class="text-red-500 text-xs mt-1">{{ numeroUnidadError
                                }}</div>
                            <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="ruta" class="block text-sm font-medium leading-6 text-gray-900">Ruta <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="ruta" :id="'ruta' + op" v-model="form.ruta"
                                    placeholder="Seleccione la ruta"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione la ruta</option>
                                    <option v-for="rut in ruta" :key="rut.idRuta" :value="rut.idRuta">
                                        {{ rut.nombreRuta }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="rutaError != ''" class="text-red-500 text-xs mt-1">{{ rutaError }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="operador"
                                class="block text-sm font-medium leading-6 text-gray-900">Operador</label>
                            <div class="mt-2">
                                <select name="operador" :id="'operador' + op" v-model="form.operador"
                                    placeholder="Seleccione al operador"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione al operador para esta unidad</option>
                                    <option v-for="chofer in operadoresDisp" :key="chofer.idOperador"
                                        :value="chofer.idOperador">
                                        {{ chofer.nombre_completo }}
                                    </option>
                                </select>
                            </div>
                            <!-- <div v-if="operadorError != ''" class="text-red-500 text-xs mt-1">{{ operadorError }}</div> -->
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="directivo" class="block text-sm font-medium leading-6 text-gray-900">Dueño de la
                                unidad <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="directivo" :id="'directivo' + op" v-model="form.directivo"
                                    placeholder="Seleccione al dueño de la unidad"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione al dueño de la unidad</option>
                                    <option v-for="dueno in directivo" :key="dueno.idDirectivo"
                                        :value="dueno.idDirectivo">
                                        {{ dueno.nombre_completo }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="directivoError != ''" class="text-red-500 text-xs mt-1">{{ directivoError }}
                            </div>
                        </div>
                    </div>
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
<style>
@import "vue-select/dist/vue-select.css";
</style>