<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { ref, computed, watch } from 'vue';
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
    operador: {
        type: Object,
        default: () => ({}),
    },
    operadoresAlta: {
        type: Object,
        default: () => ({}),
    },
    operadoresBaja: {
        type: Object,
        default: () => ({}),
    },
    estado: {
        type: Object,
        default: () => ({}),
    },
    movimiento: {
        type: Object,
        default: () => ({}),
    },
    tipoMovimiento: {
        type: Object,
        default: () => ({}),
    },
    title: { type: String },
    modal: { type: String },
    op: { type: String },
})
const emit = defineEmits(['close']);

const form = useForm({
    idMovimiento: props.movimiento.idMovimiento,
    fechaMovimiento: props.movimiento.fechaMovimiento,
    estado: props.movimiento.idEstado,
    operador: props.movimiento.idOperador,
    directivo: props.movimiento.idDirectivo,
    tipoMovimiento: props.movimiento.idTipoMovimiento
});

// Filtrar las opciones de estado para mostrar solo Alta y Baja
const estadoFiltrado = ref(props.estado.filter(e => e.idEstado === 1 || e.idEstado === 2));

// Computed property to filter `tipoMovimiento` based on selected estado
const tiposMovimientosFiltrados = computed(() => {
    if (!form.estado) {
        return [];
    }
    return props.tipoMovimiento.filter(tipo => tipo.idEstado === form.estado);
});

const operadoresFiltrados = computed(() => {
    let operadores = [];
    if (form.estado === 1) {
        operadores = props.operadoresBaja;
    } else if (form.estado === 2) {
        operadores = props.operadoresAlta;
    }
    // Ordenar alfabéticamente por nombre_completo
    return operadores.sort((a, b) => a.nombre_completo.localeCompare(b.nombre_completo));
});

const directivosFiltrados = computed(() => {
    // Ordenar alfabéticamente por nombre_completo
    return props.directivo.sort((a, b) => a.nombre_completo.localeCompare(b.nombre_completo));
});

// Variables para los mensajes de validación
const fechaError = ref('');
const estadoError = ref('');
const operadorError = ref('');
const directivoError = ref('');
const tipoMovimientoError = ref('');

// Actualizar el directivo cuando el operador cambia
watch(() => form.operador, (newOperadorId) => {
    const operadorSeleccionado = operadoresFiltrados.value.find(op => op.idOperador === newOperadorId);
    if (operadorSeleccionado) {
        form.directivo = operadorSeleccionado.idDirectivo;
    }
});

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

const validateDateField = (dateValue) => {
    // Verificar que el campo no sea null o undefined
    if (dateValue === null || dateValue === undefined) {
        return false;
    }
    // Convertir cadenas de texto a Date si es necesario
    if (typeof dateValue === 'string' || dateValue instanceof String) {
        dateValue = new Date(dateValue);
    }
    // Verificar que el valor sea una instancia de Date
    if (!(dateValue instanceof Date)) {
        return false;
    }
    // Verificar que la fecha sea válida
    if (isNaN(dateValue.getTime())) {
        return false;
    }
    return true;
};

const save = async () => {
    fechaError.value = validateDateField(form.fechaMovimiento) ? '' : 'Fecha de movimiento no válido';
    estadoError.value = validateSelect(form.estado) ? '' : 'Seleccione el movimiento a realizar';
    operadorError.value = validateSelect(form.operador) ? '' : 'Seleccione al operador';
    directivoError.value = validateSelect(form.directivo) ? '' : 'Seleccione al directivo';
    tipoMovimientoError.value = validateSelect(form.tipoMovimiento) ? '' : 'Seleccione el tipo de movimiento';

    if (
        fechaError.value || estadoError.value || operadorError.value || directivoError.value || tipoMovimientoError.value
    ) {
        return;
    }
    form.post(route('rh.addMovimiento'), {
        onSuccess: () => {
            close()
            fechaError.value = '';
            estadoError.value = '';
            operadorError.value = '';
            directivoError.value = '';
            tipoMovimientoError.value = '';
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
                    <p class="mt-1 text-sm leading-6 text-gray-600 mb-4">Rellene el formulario para poder registrar un
                        nuevo movimiento. Los campos con <span class="text-red-500">*</span> son obligatorios.
                    </p>
                    <div class="flex flex-wrap -mx-4">
                        <div class="sm:col-span-2">
                            <div class="sm:col-span-1" hidden> <!-- Definir el tamaño del cuadro de texto -->
                                <label for="idMovimiento"
                                    class="block text-sm font-medium leading-6 text-gray-900">idMovimiento</label>
                                <div class="mt-2">
                                    <input type="number" name="idMovmiento" v-model="form.idMovimiento"
                                        placeholder="Ingrese id del movimiento" :id="'idMovimiento' + op"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="fechaMovimiento" class="block text-sm font-medium leading-6 text-gray-900">Fecha
                                de Movimiento <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <input type="date" name="fechaMovimiento" :id="'fechaMovimiento' + op"
                                    v-model="form.fechaMovimiento" placeholder="Ingrese la fecha de movimiento"
                                    class="block w-44 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                            <div v-if="fechaError != ''" class="text-red-500 text-xs mt-1">{{
                                fechaError }}</div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="estado" class="block text-sm font-medium leading-6 text-gray-900">Movimiento a
                                realizar <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="tipoDirectivo" :id="'tipoDirectivo' + op" v-model="form.estado"
                                    placeholder="Seleccione el tipo de directivo"
                                    class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione el tipo de movimiento</option>
                                    <option v-for="movimiento in estadoFiltrado" :key="movimiento.idEstado"
                                        :value="movimiento.idEstado">
                                        {{ movimiento.estado }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="estadoError != ''" class="text-red-500 text-xs mt-1">{{ estadoError
                                }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="operador" class="block text-sm font-medium leading-6 text-gray-900">Operador
                                <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="operador" :id="'operador' + op" v-model="form.operador"
                                    placeholder="Seleccione al operador"
                                    class="block w-54 rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione al operador</option>
                                    <option v-for="chofer in operadoresFiltrados" :key="chofer.idOperador"
                                        :value="chofer.idOperador">
                                        {{ chofer.nombre_completo }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="operadorError != ''" class="text-red-500 text-xs mt-1">{{ operadorError
                                }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="directivo" class="block text-sm font-medium leading-6 text-gray-900">Socio /
                                Prestador <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="directivo" :id="'directivo' + op" v-model="form.directivo"
                                    placeholder="Seleccione al Socio/Prestador"
                                    class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione al Socio/Prestador</option>
                                    <option v-for="jefe in directivosFiltrados" :key="jefe.idDirectivo" :value="jefe.idDirectivo">
                                        {{ jefe.nombre_completo }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="directivoError != ''" class="text-red-500 text-xs mt-1">{{ directivoError
                                }}
                            </div>
                        </div>
                        <div class="sm:col-span-2 px-4">
                            <label for="tipoMovimiento" class="block text-sm font-medium leading-6 text-gray-900">Tipo
                                de Movimiento <span class="text-red-500">*</span></label>
                            <div class="mt-2">
                                <select name="tipoMovimiento" :id="'tipoMovimiento' + op" v-model="form.tipoMovimiento"
                                    placeholder="Seleccione el tipo de movimiento"
                                    class="block rounded-md border-0 px-1.5 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="" disabled selected>Seleccione el tipo de movimiento</option>
                                    <option v-for="tMov in tiposMovimientosFiltrados" :key="tMov.idTipoMovimiento"
                                        :value="tMov.idTipoMovimiento">
                                        {{ tMov.tipoMovimiento }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="tipoMovimientoError != ''" class="text-red-500 text-xs mt-1">{{
                                tipoMovimientoError
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