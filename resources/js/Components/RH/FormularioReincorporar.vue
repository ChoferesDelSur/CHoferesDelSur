<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, watch, onMounted } from 'vue';
import Modal from '../Modal.vue';

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
    operador: {
        type: Object,
        default: () => ({}),
    },
    operadoresIncapacidad: {
        type: Object,
        default: () => ({}),
    },
    incapacidad: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})

const emit = defineEmits(['close']);

const form = useForm({
    _method: 'PUT', // Método simulado para Laravel
    idIncapacidad: props.incapacidad.idIncapacidad,
    chofer: props.incapacidad.idOperador,
    idOperador: props.operador.idOperador,
});

watch(() => props.operador, async (newVal) => {
    form.idOperador = newVal.idOperador;
    form.chofer = newVal.idOperador;
}, { deep: true }
);
// Variables para los mensajes de validación
const operadorError = ref('');
//Funcion para cerrar el formulario
const close = async () => {
    emit('close');
    form.reset();
}
// Validación de los select 
const validateSelect = (selectedValue) => {
    if (selectedValue == undefined) {
        return false;
    }
    return true;
};

const save = async () => {
    // Validación de los campos del formulario principal
    operadorError.value = validateSelect(form.chofer) ? '' : 'Seleccione a un operador';
    // Verificar si hay algún error antes de enviar el formulario
    if (
        operadorError.value
    ) {
        return;
    }
    // Si no hay errores, enviar el formulario
    form.post(route('rh.reincorporar'), {
        onSuccess: () => {
            close();
            // Limpia los errores y campos si es necesario
            operadorError.value = '';
            // Limpia los campos de incapacidad
        }
    });
};
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <div class="mt-2 bg-white p-4 shadow rounded-lg">
            <form @submit.prevent="save">
                <div class="border-b border-gray-900/10 pb-12">
                    <h1 class="text-2xl font-semibold leading-7 text-gray-900">{{ title }}</h1>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Seleccione al operador que haya termiando sus días
                        de incapacidad y que vuelve a reincorporarse.
                    </p>
                    <!-- Campos adicionales para Incapacidad -->
                    <div class="flex flex-wrap -mx-4">
                        <div class="md:col-span-2">
                            <div class="sm:col-span-2" hidden>
                                <!-- Definir el tamaño del cuadro de texto -->
                                <label for="idIncapacidad"
                                    class="block text-sm font-medium leading-6 text-gray-900">idIncapacidad</label>
                                <div class="mt-1">
                                    <input type="number" name="idIncapacidad" v-model="form.idIncapacidad"
                                        placeholder="Ingrese id de la incapacidad" :id="'idIncapacidad' + op"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="chofer" class="block text-sm font-medium leading-6 text-gray-900">Operador <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="chofer" :id="'chofer' + op" v-model="form.chofer"
                                    placeholder="Seleccione al operador"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione al operador</option>
                                    <option v-for="chof in operadoresIncapacidad" :key="chof.idOperador"
                                        :value="chof.idOperador">
                                        {{ chof.nombre_completo }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="operadorError != ''" class="text-red-500 text-xs mt-1">{{ operadorError }}</div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" :id="'cerrar' + op"
                        class="text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-white py-2 px-2 rounded"
                        data-bs.dismiss="modal" @click="close"><i class="fa-solid fa-ban"></i> Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-2 rounded"
                        :disabled="form.processing"> <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </Modal>
</template>
<style>
@import "vue-select/dist/vue-select.css";
hr {
    border: 1px solid #e5e7eb;
    /* Ajuste del color de la línea divisoria */
}
</style>