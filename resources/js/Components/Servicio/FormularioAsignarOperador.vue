<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, watch, computed } from 'vue';
import Modal from '../Modal.vue';
import 'vue-select/dist/vue-select.css';
import vSelect from 'vue-select';
import 'vue-select';

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
    unidadesDisp: {
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

// Propiedad computada para obtener el idDirectivo de la unidad seleccionada
const idDirectivo = computed(() => {
    const unidad = props.unidadesDisp.find(u => u.idUnidad === form.unidad);
    return unidad ? unidad.idDirectivo : null;
});

// Filtrar operadores según el idDirectivo de la unidad seleccionada y ordenarlos alfabéticamente
const filtroOperadores = computed(() => {
    const directivo = idDirectivo.value;
    if (!directivo) return [];
    return props.operadoresDisp
        .filter(operador => operador.idDirectivo === directivo)
        .sort((a, b) => a.nombre_completo.localeCompare(b.nombre_completo));
});

watch(() => form.unidad, (newUnidad) => {
    // Cuando cambie la unidad, actualiza la lista de operadores filtrados
    form.operador = ''; // Limpiar el operador seleccionado
    operadorError.value = '';
}, { deep: true });

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
    unidadError.value = validateSelect(form.unidad) ? '' : 'Seleccione la unidad';
    operadorError.value = validateSelect(form.operador) ? '' : 'Seleccione el operador';

    if (
        unidadError.value || operadorError.value
    ) {
        return;
    }
    form.post(route('servicio.asignarOperador'), {
        onSuccess: () => {
            close()
            unidadError.value = '';
            operadorError.value = '';
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
    form.put(route('principal.actualizarUnidad', idUnidad), {
        onSuccess: () => {
            close()
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
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Rellene el formulario para poder asignar un
                        operador
                        a una unidad.
                    </p>
                    <div class="flex flex-wrap -mx-4">
                        <div class="sm:col-span-2 w-56 px-4">
                            <label for="unidad" class="block text-sm font-medium leading-6 text-gray-900">Unidad <span
                                    class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <v-select v-model="form.unidad" class="w-full"
                                    :options="unidadesDisp.map(u => ({ label: u.numeroUnidad, value: u.idUnidad }))"
                                    placeholder="Seleccione la unidad" :reduce="unidad => unidad.value"></v-select>
                            </div>
                            <div v-if="unidadError != ''" class="text-red-500 text-xs mt-1">{{ unidadError }}</div>
                        </div>

                        <div class="sm:col-span-2 px-4">
                            <label for="operador" class="block text-sm font-medium leading-6 text-gray-900">Operador
                                <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="operador" :id="'operador' + op" v-model="form.operador"
                                    placeholder="Seleccione al operador"
                                    class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione al operador para esta unidad</option>
                                    <option v-for="chofer in filtroOperadores" :key="chofer.idOperador"
                                        :value="chofer.idOperador">
                                        {{ chofer.nombre_completo }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="operadorError != ''" class="text-red-500 text-xs mt-1">{{ operadorError }}</div>
                        </div>
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