<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
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
    directivo: {
        type: Object,
        default: () => ({}),
    },
    tipDirectivo: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const emit = defineEmits(['close']);

const form = useForm({
    _method: 'PUT',
    idDirectivo: props.directivo.idDirectivo,
    nombre: props.directivo.nombre,
    apellidoP: props.directivo.apellidoP,
    apellidoM: props.directivo.apellidoM,
    tipDirectivo: props.directivo.idTipoDirectivo,
});

watch(() => props.directivo, async (newVal) => {
    form.idDirectivo = newVal.idDirectivo;
    form.nombre = newVal.nombre;
    form.apellidoP = newVal.apellidoP;
    form.apellidoM = newVal.apellidoM;
    form.tipDirectivo = newVal.idTipoDirectivo;
}, { deep: true }
);

// Variables para los mensajes de validación
const nombreError = ref('');
const apellidoPError = ref('');
const apellidoMError = ref('');
const tipoDirectivoError = ref('');

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

const update = async () => {
    nombreError.value = validateStringNotEmpty(form.nombre) ? '' : 'Ingrese el nombre';
    apellidoPError.value = validateStringNotEmpty(form.apellidoP) ? '' : 'Ingrese el apellido Paterno';
    apellidoMError.value = validateStringNotEmpty(form.apellidoM) ? '' : 'Ingrese el apellido Materno';
    tipoDirectivoError.value = validateSelect(form.tipDirectivo) ? '' : 'Seleccione el tipo de directivo';
    if (
        nombreError.value || apellidoPError.value || apellidoMError.value || tipoDirectivoError.value
    ) {
        return;
    }

    var idDirectivo = document.getElementById('idDirectivo2').value;
    form.post(route('rh.actualizarDirectivo', idDirectivo), {
        onSuccess: () => {
            close()
            nombreError.value = '';
            apellidoPError.value = '';
            apellidoMError.value = '';
            tipoDirectivoError.value = '';
        }
    });
}

</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="update">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Modifique los datos según sea necesario y guarde los cambios.
                    </p>
                    <input v-if="op !== '1'" type="hidden" name="_method" value="PUT">
                    <div class="flex flex-wrap -mx-4">
                        <div class="sm:col-span-2">
                            <div class="sm:col-span-1" hidden> <!-- Definir el tamaño del cuadro de texto -->
                                <label for="idDirectivo"
                                    class="block text-sm font-medium leading-6 text-gray-900">idDirectivo</label>
                                <div class="mt-2">
                                    <input type="number" name="idDirectivo" v-model="form.idDirectivo"
                                        placeholder="Ingrese id del directivo" :id="'idDirectivo' + op"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4"> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="apellidoP" class="block text-sm font-medium leading-6 text-gray-900">Apellido
                                Paterno <span class="text-red-500">*</span></label>
                            <div class="mt-2"><!-- Espacio entre titulo y cuadro de texto -->
                                <input type="text" name="apellidoP" :id="'apellidoP' + op" v-model="form.apellidoP"
                                    placeholder="Ingrese el apellido paterno"
                                    class="block w-64 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                            <!--  // Div para mostrar las validaciones en dado caso que no sean correctas -->
                            <div v-if="apellidoPError != ''" class="text-red-500 text-xs mt-1">{{ apellidoPError }}
                            </div>
                            <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="apellidoM" class="block text-sm font-medium leading-6 text-gray-900">Apellido
                                Materno <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="apellidoM" :id="'apellidoM' + op" v-model="form.apellidoM"
                                    placeholder="Ingrese el apellido materno"
                                    class="block w-64 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="apellidoMError != ''" class="text-red-500 text-xs mt-1">{{ apellidoMError }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="nombre" class="block text-sm font-medium leading-6 text-gray-900">Nombres <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="nombre" :id="'nombre' + op" v-model="form.nombre"
                                    placeholder="Ingrese el nombre"
                                    class="block w-64 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="nombreError != ''" class="text-red-500 text-xs mt-1">{{ nombreError }}</div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="tipoDirectivo" class="block text-sm font-medium leading-6 text-gray-900">Tipo de
                                Directivo <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="tipoDirectivo" :id="'tipoDirectivo' + op" v-model="form.tipDirectivo"
                                    placeholder="Seleccione el tipo de directivo"
                                    class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione un tipo de directivo</option>
                                    <option v-for="tDir in tipDirectivo" :key="tDir.idTipoDirectivo"
                                        :value="tDir.idTipoDirectivo">
                                        {{ tDir.tipoDirectivo }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="tipoDirectivoError != ''" class="text-red-500 text-xs mt-1">{{ tipoDirectivoError
                                }}
                            </div>
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