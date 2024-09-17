<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import Modal from '../Modal.vue';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';

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
    idUnidad: props.unidad.idUnidad,
    unidad: props.unidad.idUnidad,
    operador: props.unidad.idOperador,
});

watch(() => props.unidad, async (newVal) => {
    form.idUnidad = newVal.idUnidad;
    form.unidad = newVal.idUnidad;
    form.operador = newVal.idOperador;
}, { deep: true }
);

// Variables para los mensajes de validación
const unidadError = ref('');
const operadorError = ref('');

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
    unidadError.value = validateSelect(form.unidad) ? '' : 'Seleccione la unidad';

    if (
        unidadError.value
    ) {
        return;
    }
    form.post(route('servicio.quitarOperador'), {
        onSuccess: () => {
            close()
            unidadError.value = '';
        }
    })
}

const update = async () => {
    numeroUnidadError.value = validateSelect(form.unidad) ? '' : 'Seleccione la unidad';
    operadorError.value = validateSelect(form.operador) ? '' : 'Seleccione el operador';

    if (
        unidadError.value || operadorError.value
    ) {
        return;
    }

    var idUnidad = document.getElementById('idUnidad2').value;
    console.log("idUnidad:" + idUnidad);
    form.put(route('servicio.actualizarUnidad', idUnidad), {
        onSuccess: () => {
            close()
            console.log("idUnidad Editando:" + idUnidad);
            unidadError.value = '';
            operadorError.value = '';
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
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Seleccione la unidad a la que le quiere quitar el operador.
                    </p>
                    <div class="sm:col-span-2 w-56 px-4">
                            <label for="unidad" class="block text-sm font-medium leading-6 text-gray-900">Unidad</label>
                            <div class="mt-2">
                                <v-select
                                    v-model="form.unidad"
                                    :options="unidadesConOperador.map(carro => ({ label: carro.numeroUnidad, value: carro.idUnidad }))"
                                    placeholder="Seleccione la unidad"
                                    :reduce="unidad => unidad.value"
                                    class="w-full">
                                </v-select>
                            </div>
                            <div v-if="unidadError != ''" class="text-red-500 text-xs mt-1">{{ unidadError }}</div>
                        </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" :id="'cerrar' + op"
                        class="text-sm font-semibold leading-6 bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 text-white py-2 px-4 rounded"
                        data-bs.dismiss="modal" @click="close"> <i class="fa-solid fa-ban"></i> Cancelar</button>
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded"
                        :disabled="form.processing"> <i class="fa-solid fa-floppy-disk mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </Modal>
</template>