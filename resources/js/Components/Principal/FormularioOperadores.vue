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
    operador: {
        type: Object,
        default: () => ({}),
    },
    tipoOperador: {
        type: Object,
        default: () => ({}),
    },
    estado: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const emit = defineEmits(['close']);

const form = useForm({
    idOperador: props.operador.idOperador,
    nombre: props.operador.nombre,
    apellidoP: props.operador.apellidoP,
    apellidoM: props.operador.apellidoM,
    tipoOperador: props.operador.idTipoOperador,
    estado: props.operador.idEstado,
    asistencia: props.operador.idAsistencia, //Probablemente se quite al crear una tabla Asistencia
});

watch(() => props.operador, async (newVal) => {
    form.idOperador = newVal.idOperador;
    form.nombre = newVal.nombre;
    form.apellidoP = newVal.apellidoP;
    form.apellidoM = newVal.apellidoM;
    form.tipoOperador = newVal.idTipoOperador;
    form.estado = newVal.idEstado;
    form.asistencia = newVal.idAsistencia;//Probablemente se quite al crear una tabla Asistencia
}, { deep: true }
);

// Variables para los mensajes de validación
const nombreError = ref('');
const apellidoPError = ref('');
const apellidoMError = ref('');
const tipoOperadorError = ref('');
const estadoError = ref('');

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
    nombreError.value = validateStringNotEmpty(form.nombre) ? '' : 'Ingrese el nombre';
    apellidoPError.value = validateStringNotEmpty(form.apellidoP) ? '' : 'Ingrese el apellido Paterno';
    apellidoMError.value = validateStringNotEmpty(form.apellidoM) ? '' : 'Ingrese el apellido Materno';
    tipoOperadorError.value = validateSelect(form.tipoOperador) ? '' : 'Seleccione el tipo de operador';

    if (
        nombreError.value || apellidoPError.value || apellidoMError.value || tipoOperadorError.value
    ) {
        return;
    }
    form.post(route('principal.addOperador'), {
        onSuccess: () => {
            close()
            nombreError.value = '';
            apellidoPError.value = '';
            apellidoMError.value = '';
            tipoOperadorError.value = '';
        }
    })
}

const update = async () => {
    nombreError.value = validateStringNotEmpty(form.nombre) ? '' : 'Ingrese el nombre';
    apellidoPError.value = validateStringNotEmpty(form.apellidoP) ? '' : 'Ingrese el apellido Paterno';
    apellidoMError.value = validateStringNotEmpty(form.apellidoM) ? '' : 'Ingrese el apellido Materno';
    tipoOperadorError.value = validateSelect(form.tipoOperador) ? '' : 'Selecciones el tipo de operador';
}

</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="(op === '1' ? save() : update())">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Rellene todos los campos para poder registrar a un
                        nuevo operador
                    </p>
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-1" hidden> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="idOperador"
                                class="block text-sm font-medium leading-6 text-gray-900">idOperador</label>
                            <div class="mt-2">
                                <input type="number" name="idOperador" v-model="form.idOperador"
                                    placeholder="Ingrese id del operador" :id="'idOperador' + op"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2"> <!-- Definir el tamaño del cuadro de texto -->
                        <label for="apellidoP" class="block text-sm font-medium leading-6 text-gray-900">Apellido
                            Paterno</label>
                        <div class="mt-2"><!-- Espacio entre titulo y cuadro de texto -->
                            <input type="text" name="apellidoP" :id="'apellidoP' + op" v-model="form.apellidoP"
                                placeholder="Ingrese el apellido paterno"
                                class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                        <!--  // Div para mostrar las validaciones en dado caso que no sean correctas -->
                        <div v-if="apellidoPError != ''" class="text-red-500 text-xs mt-1">{{ apellidoPError }}</div>
                        <!-- //////////////////////////////////////////////////////////////////////////////////////////////// -->
                    </div>
                    <div class="sm:col-span-2">
                        <label for="apellidoM" class="block text-sm font-medium leading-6 text-gray-900">Apellido
                            Materno</label>
                        <div class="mt-2">
                            <input type="text" name="apellidoM" :id="'apellidoM' + op" v-model="form.apellidoM"
                                placeholder="Ingrese el apellido materno"
                                class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <div v-if="apellidoMError != ''" class="text-red-500 text-xs mt-1">{{ apellidoMError }}</div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="nombre" class="block text-sm font-medium leading-6 text-gray-900">Nombres</label>
                        <div class="mt-2">
                            <input type="text" name="nombre" :id="'nombre' + op" v-model="form.nombre"
                                placeholder="Ingrese el nombre"
                                class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        <div v-if="nombreError != ''" class="text-red-500 text-xs mt-1">{{ nombreError }}</div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="tipoOperador" class="block text-sm font-medium leading-6 text-gray-900">Tipo de
                            operador</label>
                        <div class="mt-2">
                            <select name="tipoOperador" :id="'tipoOperador' + op" v-model="form.tipoOperador"
                                placeholder="Seleccione el tipo de operador"
                                class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="" disabled selected>Seleccione un tipo de operador</option>
                                <option v-for="tOperador in tipoOperador" :key="tOperador.idTipoOperador"
                                    :value="tOperador.idTipoOperador">
                                    {{ tOperador.tipOperador }}
                                </option>
                            </select>
                        </div>
                        <div v-if="tipoOperadorError != ''" class="text-red-500 text-xs mt-1">{{ tipoOperadorError }}
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="estado" class="block text-sm font-medium leading-6 text-gray-900">Estado</label>
                        <div class="mt-2">
                            <select name="estado" :id="'estado' + op" v-model="form.estado"
                                placeholder="Seleccione el tipo de estado"
                                class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="" disabled selected>Seleccione el estado del operador</option>
                                <option v-for="est in estado" :key="est.idEstado" :value="est.idEstado">
                                    {{ est.estado }}
                                </option>
                            </select>
                        </div>
                        <div v-if="estadoError != ''" class="text-red-500 text-xs mt-1">{{ estadoError }}</div>
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