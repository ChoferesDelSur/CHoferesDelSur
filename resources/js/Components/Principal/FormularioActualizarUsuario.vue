<script setup>
import Modal from '../Modal.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted, watch, ref, computed } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const emit = defineEmits(['close']);

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
    _method: 'PUT',
    idUsuario: props.usuarios.idUsuario,
    nombre: props.usuarios.nombre,
    apellidoP: props.usuarios.apellidoP,
    apellidoM: props.usuarios.apellidoM,
    contrasenia: props.usuarios.contrasenia,
    tipoUsuario: props.usuarios.idTipoUsuario
});

watch(() => props.usuarios, async (newVal) => {
    form.idUsuario = newVal.idUsuario;
    form.nombre = newVal.nombre;
    form.apellidoP = newVal.apellidoP;
    form.apellidoM = newVal.apellidoM;
    form.contrasenia = newVal.contrasenia;
    form.tipoUsuario = newVal.idTipoUsuario;
}, { deep: true });


// Variables para los mensajes de validación
const nombreError = ref('');
const apellidoPError = ref('');
const apellidoMError = ref('');
const tipoUsuarioError = ref('');
const contraseniaError = ref('');

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

const validateContrasenias = (value1, value2) => {
    return value1 === value2 && value1.trim() !== '' && value2.trim() !== '';
};

const validarLargoContrasenia = (value) => {
    return typeof value === 'string' && value.length >= 8;
};

const validarComplejidadContrasenia = (value) => {
    const hasUpperCase = /[A-Z]/.test(value);
    const hasLowerCase = /[a-z]/.test(value);
    const hasNumber = /[0-9]/.test(value);
    const hasSymbol = /[@$!%*?&#]/.test(value);
    return hasUpperCase && hasLowerCase && hasNumber && hasSymbol;
};

const cumpleLargoContrasenia = computed(() => validarLargoContrasenia(form.contrasenia));
const cumpleMayuscula = computed(() => /[A-Z]/.test(form.contrasenia));
const cumpleMinuscula = computed(() => /[a-z]/.test(form.contrasenia));
const cumpleNumero = computed(() => /[0-9]/.test(form.contrasenia));
const cumpleSimbolo = computed(() => /[@$!%*?&#]/.test(form.contrasenia));

const update = () => {
    // Validaciones
    nombreError.value = validateStringNotEmpty(form.nombre) ? '' : 'Ingrese el nombre';
    apellidoPError.value = validateStringNotEmpty(form.apellidoP) ? '' : 'Ingrese el apellido paterno';
    apellidoMError.value = validateStringNotEmpty(form.apellidoM) ? '' : 'Ingrese el apellido materno';
    tipoUsuarioError.value = validateSelect(form.tipoUsuario) ? '' : 'Seleccione el tipo de usuario';
    contraseniaError.value = validateStringNotEmpty(form.contrasenia) ? '' : 'Ingrese la contraseña';

    // Verificación de errores
    if (nombreError.value || apellidoPError.value || apellidoMError.value || tipoUsuarioError.value || contraseniaError.value) {
        return;
    }

    // Construcción de los datos para enviar
    const data = {};
    if (form.nombre) data.nombre = form.nombre;
    if (form.apellidoP) data.apellidoP = form.apellidoP;
    if (form.apellidoM) data.apellidoM = form.apellidoM;
    if (form.tipoUsuario) data.tipoUsuario = form.tipoUsuario;
    if (form.contrasenia) data.contrasenia = form.contrasenia;

    form.post(route('principal.actualizarUsuario', form.idUsuario), {
        data: data,
        onSuccess: () => {
            close();
            nombreError.value = '';
            apellidoPError.value = '';
            apellidoMError.value = '';
            tipoUsuarioError.value = '';
            contraseniaError.value = '';
        }
    });
}

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">

            <form @submit.prevent="update">
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
                                    class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>

                        <div class="sm:col-span-1 md:col-span-3">
                            <label for="nombre" class="block text-sm font-medium leading-6 text-gray-900">Nombre(s)
                                <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="nombre" :id="'nombre' + op" v-model="form.nombre"
                                    placeholder="Ingrese el nombre del usuario"
                                    class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
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
                                    class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="apellidoPError != ''" class="text-red-500 text-xs mt-1">{{
                                apellidoPError }}</div>
                        </div>

                        <div class="sm:col-span-1 md:col-span-3">
                            <label for="apellidoM" class="block text-sm font-medium leading-6 text-gray-900">Apellido
                                Materno <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="text" name="apellidoM" :id="'apellidoM' + op" v-model="form.apellidoM"
                                    placeholder="Ingrese el apellido materno"
                                    class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
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
                                    class="block rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
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

                        <div class="col-span-6 sm:col-span-4 mt-2">
                            <InputLabel for="contrasenia" value="Contraseña" />
                            <div class="relative">
                                <TextInput id="contrasenia" v-model="form.contrasenia"
                                    :type="showPassword ? 'text' : 'password'" class="mt-1 block w-full"
                                    autocomplete="new-password" />
                                <button type="button"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center focus:outline-none"
                                    @click="togglePasswordVisibility">
                                    <i class="fa" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
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