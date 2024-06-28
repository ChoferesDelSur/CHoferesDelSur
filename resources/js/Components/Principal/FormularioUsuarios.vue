<script setup>
import Modal from '../Modal.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted, watch, ref } from 'vue';
const emit = defineEmits(['close']);
import axios from 'axios';

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
    usuarios: {
        type: Object,
        default: () => ({}),
    },
    tipoUsuario: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
    descripcion: { type: String },
    contrasenia: String,
},
);

const close = () => {
    emit('close');
    form.reset();
};


const form = useForm({
    idUsuario: props.usuarios.idUsuario,
    nombre: props.usuarios.nombre,
    apellidoP: props.usuarios.apellidoP,
    apellidoM: props.usuarios.apellidoM,
    tipoUsuario: props.usuarios.idTipoUsuario
});

// Variables para los mensajes de validación
const nombreError = ref('');
const apellidoPError = ref('');
const apellidoMError = ref('');
const tipoUsuarioError = ref('');

// Validación de cadenas no vacias
const validateStringNotEmpty = (value) => {
    return typeof value === 'string' && value.trim() !== '';
}

// Validación del select 
const validateSelect = (selectedValue) => {
    if (selectedValue == undefined) {
        return false;
    }
    return true;
};

const save = () => {
    nombreError.value = validateStringNotEmpty(form.nombre) ? '' : 'Ingrese el nombre';
    apellidoPError.value = validateStringNotEmpty(form.apellidoP) ? '' : 'Ingrese el apellido paterno';
    apellidoMError.value = validateStringNotEmpty(form.apellidoM) ? '' : 'Ingrese el apellido materno';
    tipoUsuarioError.value = validateSelect(form.tipoUsuario) ? '' : 'Seleccione el tipo de usuario';

    if (
        nombreError.value || apellidoPError.value || apellidoMError.value || tipoUsuarioError.value
    ) {

        return;
    }

    form.post(route('principal.addUsuario'), {
        onSuccess: () => {
            close()
            nombreError.value = '';
            apellidoPError.value = '';
            apellidoMError.value = '';
            tipoUsuarioError.value = '';
        }
    });
}

const update = () => {

    nombreError.value = validateStringNotEmpty(form.nombre) ? '' : 'Ingrese el nombre';
    apellidoPError.value = validateStringNotEmpty(form.apellidoP) ? '' : 'Ingrese el apellido paterno';
    apellidoMError.value = validateStringNotEmpty(form.apellidoM) ? '' : 'Ingrese el apellido materno';
    tipoUsuarioError.value = validateSelect(form.tipoUsuario) ? '' : 'Seleccione el tipo de usuario';

    if (
        nombreError.value || apellidoPError.value || apellidoMError.value || tipoUsuarioError.value
    ) {

        return;
    }

    var idUsuario = document.getElementById('idUsuario2').value;
    form.put(route('admin.actualizarUsuario', idUsuario), {
        onSuccess: () => {
            close()
            nombreError.value = '';
            apellidoPError.value = '';
            apellidoMError.value = '';
            tipoUsuarioError.value = '';
        }
    });
}

watch(() => props.usuarios, (newVal) => {
    form.idUsuario = newVal.idUsuario;
    form.nombre = newVal.nombre;
    form.apellidoP = newVal.apellidoP;
    form.apellidoM = newVal.apellidoM;
    form.tipoUsuario = newVal.tipoUsuario;
}, { deep: true });

</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">

            <form @submit.prevent="(op === '1' ? save() : update())">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">{{ title }}</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600"> {{ props.descripcion }}
                    </p>

                    <div class="mt-2 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="sm:col-span-1 " hidden> <!-- Definir el tamaño del cuadro de texto -->
                            <label for="idUsuario" class="block text-sm font-medium leading-6 text-gray-900">id</label>
                            <div class="mt-2">
                                <input type="number" name="idUsuario" v-model="form.idUsuario" placeholder="Ingrese id"
                                    :id="'idUsuario' + op"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:col-span-1 md:col-span-3">
                            <label for="nombre" class="block text-sm font-medium leading-6 text-gray-900">Nombre(s)
                                <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="nombre" :id="'nombre' + op" v-model="form.nombre"
                                    placeholder="Ingrese el nombre del usuario"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="nombreError != ''" class="text-red-500 text-xs mt-1">{{
                                nombreError }}</div>
                        </div>

                        <div class="sm:col-span-1 md:col-span-3">
                            <label for="apellidoP" class="block text-sm font-medium leading-6 text-gray-900">Apellido
                                Paterno <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="apellidoP" :id="'apellidoP' + op" v-model="form.apellidoP"
                                    placeholder="Ingrese el apellido paterno"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="apellidoPError != ''" class="text-red-500 text-xs mt-1">{{
                                apellidoPError }}</div>
                        </div>

                        <div class="sm:col-span-1 md:col-span-3">
                            <label for="apellidoM" class="block text-sm font-medium leading-6 text-gray-900">Apellido
                                Materno <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="apellidoP" :id="'apellidoP' + op" v-model="form.apellidoM"
                                    placeholder="Ingrese el apellido materno"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="apellidoMError != ''" class="text-red-500 text-xs mt-1">{{
                                apellidoMError }}</div>
                        </div>

                        <div class="sm:col-span-2 px-4">
                            <label for="tipoUsuario" class="block text-sm font-medium leading-6 text-gray-900">Tipo de
                                usuario <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="tipoUsuario" :id="'tipoUsuario' + op" v-model="form.tipoUsuario"
                                    placeholder="Seleccione el tipo de usuario"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione el tipo de usuario</option>
                                    <option v-for="tUsuario in tipoUsuario" :key="tUsuario.idTipoUsuario"
                                        :value="tUsuario.idTipoUsuario">
                                        {{ tUsuario.tipoUsuario }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="tipoUsuarioError != ''" class="text-red-500 text-xs mt-1">{{ tipoUsuarioError }}
                            </div>
                        </div>

                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" :id="'cerrar' + op"
                        class="text-sm font-semibold leading-6 text-white bg-red-500 hover:bg-red-600 py-2 px-4 rounded"
                        data-bs-dismiss="modal" @click="close">Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        :disabled="form.processing"> <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </Modal>
</template>